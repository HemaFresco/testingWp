<?php

namespace RtclPro\Gateways\Stripe\lib;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Models\PaymentGateway;
use RtclPro\Gateways\Stripe\GatewayStripe;
use RtclPro\Models\Subscription;
use RtclPro\Models\Subscriptions;
use Stripe\Charge;
use Stripe\Event;
use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;
use Stripe\SubscriptionSchedule;
use UnexpectedValueException;
use WP_Error;
use WP_REST_Request;

/**
 *
 * Handles webhooks from Stripe on sources that are not immediately chargeable.
 *
 * @since 4.0.0
 */
class StripeWebhook extends StripePaymentGateway {
	/**
	 * Delay of retries.
	 *
	 * @var int
	 */
	public int $retry_interval;

	/**
	 * The secret to use when verifying webhooks.
	 *
	 * @var string
	 */
	protected string $secret;

	/**
	 * @var object
	 */
	protected object $notification;
	public GatewayStripe $pmGateway;
	/**
	 * @var Event
	 */
	public Event $event;

	/**
	 * Constructor.
	 *
	 * @param GatewayStripe $gateway
	 *
	 * @since   4.0.0
	 * @version 5.0.0
	 */
	public function __construct( GatewayStripe $gateway ) {
		$this->pmGateway      = $gateway;
		$this->retry_interval = 2;
		$mode                 = $this->pmGateway->testmode ? 'test' : 'live';
		$this->secret         = $this->pmGateway->get_option( 'webhook_secret_' . $mode );
		// Get/set the time we began monitoring the health of webhooks by fetching it.
		// This should be roughly the same as the activation time of the version of the
		// plugin when this code first appears.
		StripeWebhookState::get_monitoring_began_at();
	}

	/**
	 * Check incoming requests for Stripe Webhook data and process them.
	 *
	 * @since   4.0.0
	 * @version 5.0.0
	 */
	public function check_for_webhook() {
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		$request_body    = file_get_contents( 'php://input' );
		$request_headers = array_change_key_case( $this->get_request_headers(), CASE_UPPER );

		// Validate it to make sure it is legit.
		$validation_result = $this->validate_request( $request_headers, $request_body );
		if ( StripeWebhookState::VALIDATION_SUCCEEDED === $validation_result ) {
			$notification = json_decode( $request_body );
			$data         = json_decode( $request_body, true );
			$jsonError    = json_last_error();
			if ( null === $data && \JSON_ERROR_NONE !== $jsonError ) {
				$msg = "Invalid payload: {$request_body} "
				       . "(json_last_error() was {$jsonError})";

				throw new UnexpectedValueException( $msg );
			}
			$this->notification = $notification;
			$this->event        = Event::constructFrom( $data );;

			$this->process_webhook();

			StripeWebhookState::set_last_webhook_success_at( $this->notification->created );

			status_header( 200 );
		} else {
			StripeLogger::log( 'Incoming webhook failed validation: ' . print_r( $request_body, true ) );
			StripeWebhookState::set_last_webhook_failure_at( time() );
			StripeWebhookState::set_last_error_reason( $validation_result );

			// A webhook endpoint must return a 2xx HTTP status code to prevent future webhook
			// delivery failures.
			// @see https://stripe.com/docs/webhooks/build#acknowledge-events-immediately
			status_header( 204 );
		}
		exit;
	}

	/**
	 * Verify the incoming webhook notification to make sure it is legit.
	 *
	 * @param array $request_headers The request headers from Stripe.
	 * @param       $request_body    //The request body from Stripe.
	 *
	 * @return string The validation result (e.g. self::VALIDATION_SUCCEEDED )
	 */
	public function validate_request( $request_headers, $request_body ) {
		if ( empty( $request_headers ) ) {
			return StripeWebhookState::VALIDATION_FAILED_EMPTY_HEADERS;
		}
		if ( empty( $request_body ) ) {
			return StripeWebhookState::VALIDATION_FAILED_EMPTY_BODY;
		}

		if ( empty( $this->secret ) ) {
			return $this->validate_request_user_agent( $request_headers );
		}

		// Check for a valid signature.
		$signature_format = '/^t=(?P<timestamp>\d+)(?P<signatures>(,v\d+=[a-z0-9]+){1,2})$/';
		if ( empty( $request_headers['STRIPE-SIGNATURE'] ) || ! preg_match( $signature_format, $request_headers['STRIPE-SIGNATURE'], $matches ) ) {
			return StripeWebhookState::VALIDATION_FAILED_SIGNATURE_INVALID;
		}

		// Verify the timestamp.
		$timestamp = intval( $matches['timestamp'] );
		if ( abs( $timestamp - time() ) > 5 * MINUTE_IN_SECONDS ) {
			return StripeWebhookState::VALIDATION_FAILED_TIMESTAMP_MISMATCH;
		}

		// Generate the expected signature.
		$signed_payload     = $timestamp . '.' . $request_body;
		$expected_signature = hash_hmac( 'sha256', $signed_payload, $this->secret );

		// Check if the expected signature is present.
		if ( ! preg_match( '/,v\d+=' . preg_quote( $expected_signature, '/' ) . '/', $matches['signatures'] ) ) {
			return StripeWebhookState::VALIDATION_FAILED_SIGNATURE_MISMATCH;
		}

		return StripeWebhookState::VALIDATION_SUCCEEDED;
	}

	/**
	 * Verify User Agent of the incoming webhook notification. Used as fallback for the cases when webhook secret is missing.
	 *
	 * @param array $request_headers The request headers from Stripe.
	 *
	 * @return string The validation result (e.g. self::VALIDATION_SUCCEEDED )
	 * @since   5.0.0
	 * @version 5.0.0
	 */
	private function validate_request_user_agent( $request_headers ) {
		$ua_is_valid = empty( $request_headers['USER-AGENT'] ) || preg_match( '/Stripe/', $request_headers['USER-AGENT'] );
		$ua_is_valid = apply_filters( 'rtcl_stripe_webhook_is_user_agent_valid', $ua_is_valid, $request_headers );

		return $ua_is_valid ? StripeWebhookState::VALIDATION_SUCCEEDED : StripeWebhookState::VALIDATION_FAILED_USER_AGENT_INVALID;
	}

	/**
	 * Gets the incoming request headers. Some servers are not using
	 * Apache and "getallheaders()" will not work so we may need to
	 * build our own headers.
	 *
	 * @since   4.0.0
	 * @version 4.0.0
	 */
	public function get_request_headers() {
		if ( ! function_exists( 'getallheaders' ) ) {
			$headers = [];

			foreach ( $_SERVER as $name => $value ) {
				if ( 'HTTP_' === substr( $name, 0, 5 ) ) {
					$headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value;
				}
			}

			return $headers;
		} else {
			return getallheaders();
		}
	}

	/**
	 * Process webhook payments.
	 * This is where we charge the source.
	 *
	 * @param bool $retry
	 */
	public function process_webhook_payment( bool $retry = true ) {
		// The following 3 payment methods are synchronous so does not need to be handle via webhook.
		if ( 'card' === $this->notification->data->object->type || 'sepa_debit' === $this->notification->data->object->type || 'three_d_secure' === $this->notification->data->object->type ) {
			return;
		}

		$order = StripeHelper::get_order_by_source_id( $this->notification->data->object->id );
		$order = $order ?: rtcl()->factory->get_order( $this->notification->data->object->metadata->order_id );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via source ID: ' . $this->notification->data->object->id );

			return;
		}

		$order_id = $order->get_id();

		$is_pending_receiver = ( 'receiver' === $this->notification->data->object->flow );

		try {
			if ( $order->has_status( [ 'rtcl-processing', 'rtcl-completed' ] ) ) {
				return;
			}

			if ( $order->has_status( 'rtcl-on-hold' ) && ! $is_pending_receiver ) {
				return;
			}

			// Result from Stripe API request.
			$response = null;

			// This will throw exception if not valid.
			$this->validate_minimum_order_amount( $order );

			StripeLogger::log( "Info: (Webhook) Begin processing payment for order $order_id for the amount of {$order->get_total()}" );

			// Prep source object.
			$prepared_pm_obj = $this->prepare_order_pm_obj( $order );

			$stripe = new StripeAPI();
			// Make the request.
			$response = $stripe->request( $this->generate_payment_request( $order, $prepared_pm_obj ), 'charges', 'POST', true );
			$headers  = $response['headers'];
			$response = $response['body'];

			if ( ! empty( $response->error ) ) {
				// Customer param wrong? The user may have been deleted on stripe's end. Remove customer_id. Can be retried without.
				if ( $this->is_no_such_customer_error( $response->error ) ) {
					delete_user_option( $order->get_customer_id(), '_stripe_customer_id' );
					$order->delete_meta( '_stripe_customer_id' );
				}

				// We want to retry.
				if ( $this->is_retryable_error( $response->error ) ) {
					if ( $retry ) {
						// Don't do anymore retries after this.
						if ( 5 <= $this->retry_interval ) {

							return $this->process_webhook_payment( false );
						}

						sleep( $this->retry_interval );

						$this->retry_interval ++;

						return $this->process_webhook_payment();
					} else {
						$localized_message = __( 'Sorry, we are unable to process your payment at this time. Please retry later.', 'classified-listing-pro' );
						$order->add_note( $localized_message );
						throw new StripeException( print_r( $response, true ), $localized_message );
					}
				}

				$localized_messages = StripeHelper::get_localized_messages();

				if ( 'card_error' === $response->error->type ) {
					$localized_message = $localized_messages[ $response->error->code ] ?? $response->error->message;
				} else {
					$localized_message = $localized_messages[ $response->error->type ] ?? $response->error->message;
				}

				$order->add_note( $localized_message );

				throw new StripeException( print_r( $response, true ), $localized_message );
			}

			// To prevent double processing the order on WC side.
			if ( ! $this->is_original_request( $headers ) ) {
				return;
			}

			do_action( 'rtcl_gateway_stripe_process_webhook_payment', $response, $order );

			$this->process_response( $response, $order );

		} catch ( StripeException $e ) {
			StripeLogger::log( 'Error: ' . $e->getMessage() );

			do_action( 'rtcl_gateway_stripe_process_webhook_payment_error', $order, $this->notification, $e );

			$statuses = [ 'rtcl-pending', 'rtcl-failed' ];

			if ( $order->has_status( $statuses ) ) {
				$this->send_failed_order_email( $order_id );
			}
		}
	}

	/**
	 * Process webhook dispute that is created.
	 * This is triggered when fraud is detected or customer processes chargeback.
	 * We want to put the order into on-hold and add an order note.
	 *
	 */
	public function process_webhook_dispute() {
		$order = StripeHelper::get_order_by_charge_id( $this->notification->data->object->charge );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via charge ID: ' . $this->notification->data->object->charge );

			return;
		}

		$order->update_meta( '_stripe_status_before_hold', $order->get_status() );

		/* translators: 1) The URL to the order. */
		$message = sprintf( __( 'A dispute was created for this order. Response is needed. Please go to your <a href="%s" title="Stripe Dashboard" target="_blank">Stripe Dashboard</a> to review this dispute.', 'classified-listing-pro' ), $this->get_transaction_url( $order ) );
		if ( ! $order->get_meta( '_stripe_status_final' ) ) {
			$order->update_status( 'rtcl-on-hold', $message );
		} else {
			$order->add_note( $message );
		}

		do_action( 'rtcl_gateway_stripe_process_webhook_payment_error', $order, $this->notification );

		$order_id = $order->get_id();
		$this->send_failed_order_email( $order_id );
	}

	/**
	 * Process webhook dispute that is closed.
	 *
	 */
	public function process_webhook_dispute_closed() {
		$order  = StripeHelper::get_order_by_charge_id( $this->notification->data->object->charge );
		$status = $this->notification->data->object->status;

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via charge ID: ' . $this->notification->data->object->charge );

			return;
		}

		if ( 'lost' === $status ) {
			$message = __( 'The dispute was lost or accepted.', 'classified-listing-pro' );
		} elseif ( 'won' === $status ) {
			$message = __( 'The dispute was resolved in your favor.', 'classified-listing-pro' );
		} elseif ( 'warning_closed' === $status ) {
			$message = __( 'The inquiry or retrieval was closed.', 'classified-listing-pro' );
		} else {
			return;
		}

		if ( apply_filters( 'rtcl_stripe_webhook_dispute_change_order_status', true, $order, $this->notification ) ) {
			// Mark final so that order status is not overridden by out-of-sequence events.
			$order->update_meta( '_stripe_status_final', true );

			// Fail order if dispute is lost, or else revert to pre-dispute status.
			$order_status = 'lost' === $status ? 'failed' : $order->get_meta( '_stripe_status_before_hold', true, 'processing' );
			$order->update_status( $order_status, $message );
		} else {
			$order->add_note( $message );
		}
	}

	/**
	 * Process webhook capture. This is used for an authorized only
	 * transaction that is later captured via Stripe not WC.
	 *
	 */
	public function process_webhook_capture() {
		$order = StripeHelper::get_order_by_charge_id( $this->notification->data->object->id );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via charge ID: ' . $this->notification->data->object->id );

			return;
		}

		if ( 'stripe' === $order->get_payment_method() ) {
			$charge   = $order->get_transaction_id();
			$captured = $order->get_meta( '_stripe_charge_captured' );

			if ( $charge && 'no' === $captured ) {
				$order->update_meta( '_stripe_charge_captured', 'yes' );

				// Store other data such as fees
				$order->set_transaction_id( $this->notification->data->object->id );

				if ( isset( $this->notification->data->object->balance_transaction ) ) {
					$this->update_fees( $order, $this->notification->data->object->balance_transaction );
				}

				// Check and see if capture is partial.
				if ( $this->is_partial_capture() ) {
					$partial_amount = $this->get_partial_amount_to_charge();
					$order->set_total( $partial_amount );
					$this->update_fees( $order, $this->notification->data->object->refunds->data[0]->balance_transaction );
					/* translators: partial captured amount */
					$order->add_note( sprintf( __( 'This charge was partially captured via Stripe Dashboard in the amount of: %s', 'classified-listing-pro' ), $partial_amount ) );
				} else {
					$order->payment_complete( $this->notification->data->object->id );

					/* translators: transaction id */
					$order->add_note( sprintf( __( 'Stripe charge complete (Charge ID: %s)', 'classified-listing-pro' ), $this->notification->data->object->id ) );
				}
			}
		}
	}

	/**
	 * Process webhook charge succeeded.
	 *
	 * @deprecated
	 */
	public function process_webhook_charge_succeeded() {
		$this->process_charge_succeeded( $this->event->data->object );
	}

	/**
	 * Process webhook charge failed.
	 *
	 * @since 4.1.5 Can handle any fail payments from any methods.
	 * @since 4.0.0
	 */
	public function process_webhook_charge_failed() {
		$order = StripeHelper::get_order_by_charge_id( $this->notification->data->object->id );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via charge ID: ' . $this->notification->data->object->id );

			return;
		}

		// If order status is already in failed status don't continue.
		if ( $order->has_status( 'rtcl-failed' ) ) {
			return;
		}

		$message = __( 'This payment failed to clear.', 'classified-listing-pro' );
		if ( ! $order->get_meta( '_stripe_status_final' ) ) {
			$order->update_status( 'rtcl-failed', $message );
		} else {
			$order->add_note( $message );
		}

		do_action( 'rtcl_gateway_stripe_process_webhook_payment_error', $order, $this->notification );
	}

	/**
	 * Process webhook source canceled. This is used for payment methods
	 * that redirects and awaits payments from customer.
	 *
	 */
	public function process_webhook_source_canceled() {
		$order = StripeHelper::get_order_by_charge_id( $this->notification->data->object->id );

		// If can't find order by charge ID, try source ID.
		if ( ! $order ) {
			$order = StripeHelper::get_order_by_source_id( $this->notification->data->object->id );

			if ( ! $order ) {
				StripeLogger::log( 'Could not find order via charge/source ID: ' . $this->notification->data->object->id );

				return;
			}
		}

		// Don't proceed if payment method isn't Stripe.
		if ( 'stripe' !== $order->get_payment_method() ) {
			StripeLogger::log( 'Canceled webhook abort: Order was not processed by Stripe: ' . $order->get_id() );

			return;
		}

		$message = __( 'This payment was cancelled.', 'classified-listing-pro' );
		if ( ! $order->has_status( 'rtcl-cancelled' ) && ! $order->get_meta( '_stripe_status_final' ) ) {
			$order->update_status( 'rtcl-cancelled', $message );
		} else {
			$order->add_note( $message );
		}

		do_action( 'rtcl_gateway_stripe_process_webhook_payment_error', $order, $this->notification );
	}

	/**
	 * Process webhook refund.
	 *
	 *
	 */
	public function process_webhook_refund() {
		$order = StripeHelper::get_order_by_charge_id( $this->notification->data->object->id );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via charge ID: ' . $this->notification->data->object->id );

			return;
		}

		return;
		//Need to apply refund code

		$order_id = $order->get_id();

		if ( 'stripe' === $order->get_payment_method() ) {
			$charge    = $order->get_transaction_id();
			$captured  = $order->get_meta( '_stripe_charge_captured' );
			$refund_id = $order->get_meta( '_stripe_refund_id' );

			$amount = Functions::get_payment_formatted_price( $this->notification->data->object->refunds->data[0]->amount / 100 );
			if ( in_array( strtolower( $order->get_currency() ), StripeHelper::no_decimal_currencies() ) ) {
				$amount = Functions::get_payment_formatted_price( $this->notification->data->object->refunds->data[0]->amount );
			}

			// If charge wasn't captured, skip creating a refund.
			if ( 'yes' !== $captured ) {
				// If the process was initiated from wp-admin,
				// the order was already cancelled, so we don't need a new note.
				if ( 'rtcl-cancelled' !== $order->get_status() ) {
					/* translators: amount (including currency symbol) */
					$order->add_note( sprintf( __( 'Pre-Authorization for %s voided from the Stripe Dashboard.', 'classified-listing-pro' ), $amount ) );
					$order->update_status( 'rtcl-cancelled' );
				}

				return;
			}

			// If the refund ID matches, don't continue to prevent double refunding.
			if ( $this->notification->data->object->refunds->data[0]->id === $refund_id ) {
				return;
			}

			if ( $charge ) {
				$reason = __( 'Refunded via Stripe Dashboard', 'classified-listing-pro' );

				// Create the refund.
				$refund = rtcl_create_refund(
					[
						'order_id' => $order_id,
						'amount'   => $this->get_refund_amount( $this->notification ),
						'reason'   => $reason,
					]
				);

				if ( is_wp_error( $refund ) ) {
					StripeLogger::log( $refund->get_error_message() );
				}

				$order->update_meta( '_stripe_refund_id', $this->notification->data->object->refunds->data[0]->id );

				if ( isset( $this->notification->data->object->refunds->data[0]->balance_transaction ) ) {
					$this->update_fees( $order, $this->notification->data->object->refunds->data[0]->balance_transaction );
				}

				/* translators: 1) amount (including currency symbol) 2) transaction id 3) refund message */
				$order->add_note( sprintf( __( 'Refunded %1$s - Refund ID: %2$s - %3$s', 'classified-listing-pro' ), $amount, $this->notification->data->object->refunds->data[0]->id, $reason ) );
			}
		}
	}

	/**
	 * Process webhook reviews that are opened. i.e Radar.
	 *
	 * @since 4.0.6
	 */
	public function process_review_opened() {
		if ( isset( $this->notification->data->object->payment_intent ) ) {
			$order = StripeHelper::get_order_by_intent_id( $this->notification->data->object->payment_intent );

			if ( ! $order ) {
				StripeLogger::log( '[Review Opened] Could not find order via intent ID: ' . $this->notification->data->object->payment_intent );

				return;
			}
		} else {
			$order = StripeHelper::get_order_by_charge_id( $this->notification->data->object->charge );

			if ( ! $order ) {
				StripeLogger::log( '[Review Opened] Could not find order via charge ID: ' . $this->notification->data->object->charge );

				return;
			}
		}

		$order->update_meta( '_stripe_status_before_hold', $order->get_status() );

		/* translators: 1) The URL to the order. 2) The reason type. */
		$message = sprintf( __( 'A review has been opened for this order. Action is needed. Please go to your <a href="%1$s" title="Stripe Dashboard" target="_blank">Stripe Dashboard</a> to review the issue. Reason: (%2$s)', 'classified-listing-pro' ), $this->get_transaction_url( $order ), $this->notification->data->object->reason );

		if ( apply_filters( 'rtcl_stripe_webhook_review_change_order_status', true, $order, $this->notification ) && ! $order->get_meta( '_stripe_status_final' ) ) {
			$order->update_status( 'rtcl-on-hold', $message );
		} else {
			$order->add_note( $message );
		}
	}

	/**
	 * Process webhook reviews that are closed. i.e Radar.
	 *
	 */
	public function process_review_closed() {
		if ( isset( $this->notification->data->object->payment_intent ) ) {
			$order = StripeHelper::get_order_by_intent_id( $this->notification->data->object->payment_intent );

			if ( ! $order ) {
				StripeLogger::log( '[Review Closed] Could not find order via intent ID: ' . $this->notification->data->object->payment_intent );

				return;
			}
		} else {
			$order = StripeHelper::get_order_by_charge_id( $this->notification->data->object->charge );

			if ( ! $order ) {
				StripeLogger::log( '[Review Closed] Could not find order via charge ID: ' . $this->notification->data->object->charge );

				return;
			}
		}

		/* translators: 1) The reason type. */
		$message = sprintf( __( 'The opened review for this order is now closed. Reason: (%s)', 'classified-listing-pro' ), $this->notification->data->object->reason );

		if (
			$order->has_status( 'rtcl-on-hold' ) &&
			apply_filters( 'rtcl_stripe_webhook_review_change_order_status', true, $order, $this->notification ) &&
			! $order->get_meta( '_stripe_status_final' )
		) {
			$order->update_status( $order->get_meta( '_stripe_status_before_hold', true, 'processing' ), $message );
		} else {
			$order->add_note( $message );
		}
	}

	/**
	 * Checks if capture is partial.
	 *
	 */
	public function is_partial_capture() {
		return 0 < $this->notification->data->object->amount_refunded;
	}

	/**
	 * Gets the amount refunded.
	 *
	 */
	public function get_refund_amount() {
		if ( $this->is_partial_capture() ) {
			$amount = $this->notification->data->object->refunds->data[0]->amount / 100;

			if ( in_array( strtolower( $this->notification->data->object->currency ), StripeHelper::no_decimal_currencies() ) ) {
				$amount = $this->notification->data->object->refunds->data[0]->amount;
			}

			return $amount;
		}

		return false;
	}

	/**
	 * Gets the amount we actually charge.
	 *
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	public function get_partial_amount_to_charge() {
		if ( $this->is_partial_capture() ) {
			$amount = ( $this->notification->data->object->amount - $this->notification->data->object->amount_refunded ) / 100;

			if ( in_array( strtolower( $this->notification->data->object->currency ), StripeHelper::no_decimal_currencies() ) ) {
				$amount = ( $this->notification->data->object->amount - $this->notification->data->object->amount_refunded );
			}

			return $amount;
		}

		return false;
	}

	/**
	 * @throws StripeException
	 */
	public function process_payment_intent_success() {
		$intent = $this->notification->data->object;
		$order  = StripeHelper::get_order_by_intent_id( $intent->id );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via intent ID: ' . $intent->id );

			return;
		}

		if ( ! $order->has_status( apply_filters( 'rtcl_stripe_allowed_payment_processing_statuses', [
			'rtcl-pending',
			'rtcl-failed'
		] ) ) ) {
			return;
		}

		if ( $this->lock_order_payment( $order, $intent ) ) {
			return;
		}

		$order_id = $order->get_id();
		if ( 'payment_intent.succeeded' === $this->notification->type || 'payment_intent.amount_capturable_updated' === $this->notification->type ) {
			$charge = end( $intent->charges->data );
			StripeLogger::log( "Stripe PaymentIntent $intent->id succeeded for order $order_id" );

			do_action( 'rtcl_gateway_stripe_process_payment', $charge, $order );

			// Process valid response.
			$this->process_response( $charge, $order );

		} else {
			$error_message = $intent->last_payment_error ? $intent->last_payment_error->message : '';

			/* translators: 1) The error message that was received from Stripe. */
			$message = sprintf( __( 'Stripe SCA authentication failed. Reason: %s', 'classified-listing-pro' ), $error_message );

			if ( ! $order->get_meta( '_stripe_status_final' ) ) {
				$order->update_status( 'failed', $message );
			} else {
				$order->add_note( $message );
			}

			do_action( 'rtcl_gateway_stripe_process_webhook_payment_error', $order, $this->notification );

			//$this->send_failed_order_email($order_id); TODO : need to add email here
		}

		$this->unlock_order_payment( $order );
	}

	public function process_setup_intent() {
		$intent = $this->notification->data->object;
		$order  = StripeHelper::get_order_by_setup_intent_id( $intent->id );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via setup intent ID: ' . $intent->id );

			return;
		}

		if ( ! $order->has_status(
			apply_filters(
				'rtcl_gateway_stripe_allowed_payment_processing_statuses',
				[ 'rtcl-pending', 'rtcl-failed' ]
			)
		) ) {
			return;
		}

		if ( $this->lock_order_payment( $order, $intent ) ) {
			return;
		}

		$order_id = $order->get_id();
		if ( 'setup_intent.succeeded' === $this->notification->type ) {
			StripeLogger::log( "Stripe SetupIntent $intent->id succeeded for order $order_id" );
			$order->payment_complete();
		} else {
			$error_message = $intent->last_setup_error ? $intent->last_setup_error->message : '';

			/* translators: 1) The error message that was received from Stripe. */
			$message = sprintf( __( 'Stripe SCA authentication failed. Reason: %s', 'classified-listing-pro' ), $error_message );

			if ( ! $order->get_meta( '_stripe_status_final' ) ) {
				$order->update_status( 'rtcl-failed', $message );
			} else {
				$order->add_note( $message );
			}

			$this->send_failed_order_email( $order_id );
		}

		$this->unlock_order_payment( $order );
	}

	/**
	 * Processes the incoming webhook.
	 *
	 */
	public function process_webhook() {

		switch ( $this->notification->type ) {
			case 'source.chargeable':
				$this->process_webhook_payment();
				break;

			case 'source.canceled':
				$this->process_webhook_source_canceled();
				break;

			case 'charge.succeeded':
				$this->process_charge_succeeded( $this->event->data->object );
				break;

			case 'charge.failed':
				$this->process_webhook_charge_failed();
				break;

			case 'charge.captured':
				$this->process_webhook_capture();
				break;

			case 'charge.dispute.created':
				$this->process_webhook_dispute();
				break;

			case 'charge.dispute.closed':
				$this->process_webhook_dispute_closed();
				break;

			case 'charge.refunded':
				$this->process_webhook_refund();
				break;

			case 'review.opened':
				$this->process_review_opened();
				break;

			case 'review.closed':
				$this->process_review_closed();
				break;

			case 'payment_intent.succeeded':
			case 'payment_intent.payment_failed':
			case 'payment_intent.amount_capturable_updated':
				$this->process_payment_intent_success();
				break;

			case 'setup_intent.succeeded':
			case 'setup_intent.setup_failed':
				$this->process_setup_intent();
				break;

			case 'subscription_schedule.canceled':
				$this->process_subscription_canceled( $this->event->data->object );
				break;

			case 'subscription_schedule.updated':
				$this->process_subscription_updated( $this->event->data->object );
				break;
			default:
				break;

		}
	}


	/**
	 * Checks if request is the original to prevent double processing
	 * on WC side. The original-request header and request-id header
	 * needs to be the same to mean its the original request.
	 *
	 * @param array $headers
	 *
	 * @return bool
	 */
	public function is_original_request( array $headers ): bool {
		if ( $headers['original-request'] === $headers['request-id'] ) {
			return true;
		}

		return false;
	}


	/**
	 * When the charge has succeeded, the order should be completed.
	 *
	 * @param Charge $charge
	 *
	 * @since   2.1.4
	 */
	public function process_charge_succeeded( Charge $charge ) {
		if ( $charge->invoice ) {
			try {
				$invoice = $this->pmGateway->stripe->invoices->retrieve( $charge->invoice );
				if ( ! empty( $invoice->subscription ) ) {
					$subscriptionIn = ( new Subscriptions() )->findOneBySubId( $invoice->subscription );

					if ( ! $subscriptionIn ) {
						StripeLogger::error( 'Could not find subscription via charge ID: ' . $invoice->subscription );

						return;
					}
					$this->createOrderForRecurringTransaction( $subscriptionIn, $charge, $invoice );

					return;
				}

			} catch ( ApiErrorException $exception ) {
				StripeLogger::error( $exception->getMessage() );
			}
		}

		// Ignore the notification for charges, created through PaymentIntents.
		if ( ! empty( $charge->payment_intent ) ) {
			return;
		}

		// The following payment methods are synchronous so does not need to be handle via webhook.
		if ( ( isset( $charge->source->type ) && 'card' === $charge->source->type ) || ( isset( $charge->source->type ) && 'three_d_secure' === $charge->source->type ) ) {
			return;
		}

		$order = StripeHelper::get_order_by_charge_id( $charge->id );

		if ( ! $order ) {
			StripeLogger::log( 'Could not find order via charge ID: ' . $charge->id );

			return;
		}

		if ( ! $order->has_status( 'rtcl-on-hold' ) ) {
			return;
		}

		// Store other data such as fees
		$order->set_transaction_id( $charge->id );

		if ( isset( $charge->balance_transaction ) ) {
			$this->update_fees( $order, $charge->balance_transaction );
		}

		$order->payment_complete( $charge->id );
		/* translators: transaction id */
		$order->add_note( sprintf( __( 'Charge.succeeded webhook received. Stripe charge complete (Charge ID: %s)', 'classified-listing-pro' ), $charge->id ) );
	}


	/**
	 * When the SubscriptionSchedule has updated
	 *
	 * @param SubscriptionSchedule $subscription
	 *
	 * @since   2.1.4
	 */
	public function process_subscription_canceled( SubscriptionSchedule $subscription ) {
		$subscriptionIn = ( new Subscriptions() )->findOneBySubId( $subscription->id );

		if ( ! $subscriptionIn ) {
			StripeLogger::log( 'Could not find subscription via charge ID: ' . $subscription->id );

			return;
		}
		$subscriptionIn->updateStatus( Subscription::STATUS_CANCELED );
	}


	/**
	 * When the SubscriptionSchedule has updated
	 *
	 * @param SubscriptionSchedule $subscription
	 *
	 * @since   2.1.4
	 */
	public function process_subscription_updated( SubscriptionSchedule $subscription ) {
		$subscriptionIn = ( new Subscriptions() )->findOneBySubId( $subscription->id );

		if ( ! $subscriptionIn ) {
			StripeLogger::log( 'Could not find subscription via charge ID: ' . $subscription->id );

			return;
		}

		if ( $subscription->status != $subscriptionIn->getStatus() ) {
			$subscriptionIn->updateStatus( $subscription->status );
		}
	}

	/**
	 * @param Subscription $subscriptionIn
	 * @param Charge       $charge
	 * @param Invoice      $invoice
	 *
	 * @return void
	 */
	public function createOrderForRecurringTransaction( Subscription $subscriptionIn, Charge $charge, Invoice $invoice ): void {
		$product = rtcl()->factory->get_pricing( $subscriptionIn->getProductId() );
		if ( ! $product ) {
			new WP_Error( 'rtcl_webhook_gateway_' . $this->id . '_error', __( 'Product not found while creating order for subscription transaction', 'classified-listing-pro' ) );

			return;
		}
		$user       = get_user_by( 'ID', $subscriptionIn->getUserId() );
		$metaInputs = [
			'customer_id'           => $user->ID,
			'_order_key'            => apply_filters( 'rtcl_generate_order_key', uniqid( 'rtcl_oder_' ) ),
			'_pricing_id'           => $product->getId(),
			'amount'                => $invoice->total,
			'_payment_method'       => $this->id,
			'_payment_method_title' => $this->method_title,
			'_order_currency'       => $invoice->currency,
			'_billing_email'        => ! empty( $invoice->customer_email ) ? $invoice->customer_email : $user->user_email
		];

		if ( ! empty( $invoice->customer_name ) ) {
			$metaInputs['_billing_first_name'] = $invoice->customer_name;
		} else {
			$metaInputs['_billing_first_name'] = $user->first_name;
		}
		$metaInputs['_billing_last_name'] = $user->last_name;

		if ( ! empty( $invoice->customer_city ) ) {
			$metaInputs['_billing_city'] = $invoice->customer_city;
		}
		if ( ! empty( $invoice->customer_state ) ) {
			$metaInputs['_billing_state'] = $invoice->customer_state;
		}
		if ( ! empty( $invoice->customer_phone ) ) {
			$metaInputs['_billing_phone'] = $invoice->customer_phone;
		}
		if ( ! empty( $invoice->customer_address ) ) {
			$metaInputs['_billing_address_1'] = $invoice->customer_address;
		}

		$newOrderArgs = [
			'post_title'  => esc_html__( 'Subscription recurring: Order on', 'classified-listing' ) . ' ' . current_time( "l jS F Y h:i:s A" ),
			'post_status' => 'rtcl-created',
			'post_parent' => '0',
			'ping_status' => 'closed',
			'post_author' => 1,
			'post_type'   => rtcl()->post_type_payment,
			'meta_input'  => $metaInputs
		];

		$order_id = wp_insert_post( apply_filters( 'rtcl_webhook_' . $this->id . '_new_order_args', $newOrderArgs, $invoice, $subscriptionIn ) );
		if ( is_wp_error( $order_id ) ) {
			new WP_Error( 'rtcl_webhook_gateway_' . $this->id . '_error', __( 'Error while creating new order for subscription recurring.', 'classified-listing-pro' ) );

			return;
		}
		$order = rtcl()->factory->get_order( $order_id );
		$order->set_order_key();
		do_action( 'rtcl_checkout_process_new_payment_created', $order_id, $order );

		if ( ! empty( $charge->payment_method ) ) {
			update_post_meta( $order->get_id(), '_stripe_pm_id', $charge->payment_method );
		}
		if ( ! empty( $charge->payment_intent ) ) {
			update_post_meta( $order->get_id(), '_stripe_intent_id', $charge->payment_intent );
		}

		$order->payment_complete( $charge->id );
		$message = sprintf( __( 'Charge.succeeded webhook received. Stripe charge complete (Charge ID: %s)', 'classified-listing-pro' ), $charge->id );
		$order->add_note( $message );
	}
}
