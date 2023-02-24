<?php

namespace RtclPro\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Traits\SingletonTrait;
use RtclPro\Gateways\Authorize\lib\AuthNetAPI;
use RtclPro\Gateways\Authorize\lib\Types\CreditCardType;
use RtclPro\Gateways\Authorize\lib\Types\PaymentType;
use RtclPro\Gateways\Authorize\lib\Types\SubscriptionType;
use RtclPro\Gateways\Stripe\lib\StripeAPI;
use RtclPro\Gateways\Stripe\lib\StripeCustomer;
use RtclPro\Gateways\Stripe\lib\StripeException;
use RtclPro\Models\Subscription;
use RtclPro\Models\Subscriptions;
use WP_User;

class SubscriptionController {

	use SingletonTrait;

	public function __construct() {
		add_action( 'rtcl_account_dashboard', [ &$this, 'subscription_report' ], 20 );
		add_action( 'wp_ajax_rtcl_subscription_cancel', [ &$this, 'subscription_cancel' ] );
		add_action( 'wp_ajax_rtcl_subscription_update_payment', [ &$this, 'subscription_update_payment' ] );
	}

	/**
	 * @return void
	 */
	public function subscription_cancel() {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! $id ) {
			wp_send_json_error( esc_html__( 'Subscription id is mission', 'classified-listing-pro' ) );
		}

		$subscription = ( new Subscriptions() )->findById( $id );
		if ( empty( $subscription ) ) {
			wp_send_json_error( esc_html__( 'No subscription found to cancel', 'classified-listing-pro' ) );
		}

		$gateway  = Functions::get_payment_gateway( $subscription->getGatewayId() );
		$response = $gateway->cancelSubscription( $subscription );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( $response->get_error_message() );
		}


		wp_send_json_success( [
			'id'      => $id,
			'message' => __( 'Subscription cancel successfully.', 'classified-listing-pro' )
		] );

	}

	public function subscription_update_payment() {
		$id = isset( $_POST['_subscription_id'] ) ? absint( $_POST['_subscription_id'] ) : 0;
		if ( ! $id ) {
			wp_send_json_error( esc_html__( 'Subscription id is mission', 'classified-listing-pro' ) );
		}
		$subscription = ( new Subscriptions() )->findById( $id );
		if ( empty( $subscription ) ) {
			wp_send_json_error( esc_html__( 'No subscription found to cancel', 'classified-listing-pro' ) );
		}

		$gateway = Functions::get_payment_gateway( $subscription->getGatewayId() );
		if ( $gateway->id === 'stripe' || $gateway->id === 'stripe_cc' ) {

			$pm_id = isset( $_POST['pm_id'] ) ? Functions::clean( $_POST['pm_id'] ) : null;
			if ( empty( $pm_id ) ) {
				wp_send_json_error( esc_html__( 'No payment method id found.', 'classified-listing-pro' ) );
			}

			if ( ! preg_match( '/^pm_.*$/', $pm_id ) ) {
				wp_send_json_error( $pm_id );
			}

			$response = $gateway->updateSubscriptionPaymentMethod( $subscription, $pm_id );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( $response->get_error_message() );
			}

			$response['message'] = __( 'Subscription payment card information updated.', 'classified-listing-pro' );
			wp_send_json_success( $response );
		} else if ( $gateway->id === 'authorizenet' ) {
			if ( empty( $_POST['authorizenet-card-number'] ) || empty( $_POST['authorizenet-card-expiry'] ) || empty( $_POST['authorizenet-card-cvc'] ) ) {
				wp_send_json_error( esc_html__( 'Please select card field', 'classified-listing-pro' ) );
			}
			$card_number = sanitize_text_field( str_replace( ' ', '', $_POST['authorizenet-card-number'] ) );
			$card_expiry = trim( sanitize_text_field( $_POST['authorizenet-card-expiry'] ) );
			$card_cvc    = sanitize_text_field( $_POST['authorizenet-card-cvc'] );


			$cardtype = $gateway->get_card_type( $card_number );
			if ( ! in_array( $cardtype, $gateway->getAuthorizenetCardTypes() ) ) {
				$message = sprintf( esc_html__( 'Merchant do not support accepting in %s', 'classified-listing-pro' ), $cardtype );
				wp_send_json_error( $message );
			}

			$exp_year = $exp_date = $exp_month = null;
			if ( $card_expiry && strpos( $card_expiry, '/' ) !== false ) {
				$exp_date  = explode( "/", $card_expiry );
				$exp_month = ! empty( $exp_date[0] ) ? trim( $exp_date[0] ) : null;
				$exp_year  = ! empty( $exp_date[1] ) ? trim( $exp_date[1] ) : null;
			} else if ( $card_expiry && strpos( $card_expiry, '-' ) !== false ) {
				$exp_date  = explode( "-", $card_expiry );
				$exp_month = ! empty( $exp_date[0] ) ? trim( $exp_date[0] ) : null;
				$exp_year  = ! empty( $exp_date[1] ) ? trim( $exp_date[1] ) : null;
			}
			if ( $exp_year && strlen( $exp_year ) == 2 ) {
				$exp_year += 2000;
			}
			if ( $exp_year && $exp_month ) {
				$exp_date = $exp_year . '-' . $exp_month;
			}
			if ( empty( $exp_date ) || empty( $card_number ) || empty( $card_cvc ) ) {
				wp_send_json_error( esc_html__( 'Please select card field', 'classified-listing-pro' ) );
			}

			$creditCard = new CreditCardType();
			$creditCard->setCardNumber( $card_number );
			$creditCard->setExpirationDate( $exp_date );
			$creditCard->setCardCode( $card_cvc );

			$payment = new PaymentType();
			$payment->setCreditCard( $creditCard );

			$response = $gateway->updateSubscriptionPayment( $subscription, $payment );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( $response->get_error_message() );
			}
			$response['message'] = __( 'Subscription payment card information updated.', 'classified-listing-pro' );
			wp_send_json_success( $response );
		}
		wp_send_json_error( __( "Payment gateway not found.", 'classified-listing-pro' ) );
	}

	/**
	 * @param WP_User $current_user
	 *
	 * @return void
	 */
	public function subscription_report( WP_User $current_user ) {
		$subscriptions = ( new Subscriptions() )->findAllByUserId( $current_user->ID );
		if ( ! empty( $subscriptions ) ) {
			Functions::get_template( 'myaccount/subscription-report', compact( 'subscriptions', 'current_user' ), '', rtclPro()->get_plugin_template_path() );
		}

	}
}