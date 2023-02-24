<?php

namespace RtclPro\Api\V1;

use Exception;
use Rtcl\Helpers\Functions;
use RtclPro\Api\RestController;
use RtclPro\Gateways\Stripe\lib\StripeLogger;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class V1_WebHookApi extends RestController {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->namespace = 'rtcl/v1';
		$this->rest_base = 'webhook';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, $this->rest_base . '/gateway/stripe',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'handle_stripe_gateway' ],
					'permission_callback' => '__return_true'
				],
			]
		);
	}

	public function handle_stripe_gateway( WP_REST_Request $request ) {
		$payload       = $request->get_body();
		$json_payload  = json_decode( $payload, true );
		$mode          = ! empty( $json_payload['livemode'] ) ? 'live' : 'test';
		$stripeGateway = Functions::get_payment_gateway( 'stripe' );
		$secret        = $stripeGateway->get_option( 'webhook_secret_' . $mode );
		$header        = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
		try {
			$event = Webhook::constructEvent( $payload, $header, $secret, apply_filters( 'rtcl_stripe_webhook_signature_tolerance', 600 ) );

			StripeLogger::log( sprintf( 'Webhook notification received: Event: %s', $event->type ) );
			$type = $event->type;
			$type = str_replace( '.', '_', $type );

			// allow functionality to hook in to the event action
			do_action( 'rtcl_stripe_webhook_' . $type, $event->data->object, $request, $event );

			return rest_ensure_response( apply_filters( 'rtcl_stripe_webhook_response', [], $event, $request ) );
		} catch ( SignatureVerificationException $e ) {
			StripeLogger::error( sprintf( __( 'Invalid signature received. Verify that your webhook secret is correct. Error: %s', 'classified-listing-pro' ), $e->getMessage() ) );

			return $this->send_error_response( __( 'Invalid signature received. Verify that your webhook secret is correct.', 'classified-listing-pro' ), 401 );
		} catch ( Exception $e ) {
			StripeLogger::error( sprintf( __( 'Error processing webhook. Message: %s Exception: %s', 'classified-listing-pro' ), $e->getMessage(), get_class( $e ) ) );

			return $this->send_error_response( $e->getMessage() );
		}
	}


	private function send_error_response( $message, $code = 400 ): WP_Error {
		return new WP_Error( 'rtcl_webhook_error', $message, [ 'status' => $code ] );
	}
}