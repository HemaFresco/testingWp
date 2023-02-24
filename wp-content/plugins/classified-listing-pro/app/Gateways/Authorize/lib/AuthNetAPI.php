<?php

namespace RtclPro\Gateways\Authorize\lib;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use RtclPro\Gateways\Authorize\lib\Types\CreditCardType;
use RtclPro\Gateways\Authorize\lib\Types\SubscriptionType;
use RtclPro\Gateways\Authorize\lib\Types\CreditCardSimpleType;
use RtclPro\Gateways\Authorize\lib\Types\TransactionType;
use WP_Error;
use Exception;

//https://wordpress.org/plugins/authnet-cim-for-woo/#developers
class AuthNetAPI {

	/**
	 * Stores the gateway url.
	 *
	 * @var string
	 */
	private string $url;

	/**
	 * Stores the api login.
	 *
	 * @var string
	 */
	private string $api_login;

	/**
	 * @var string
	 */
	private $transaction_key;

	/**
	 * Stores the transaction key.
	 *
	 * @var TransactionType
	 */
	private TransactionType $transaction;


	protected Payment $order;

	protected SubscriptionType $subscriptionType;


	/**
	 * Constructor
	 */
	public function __construct( $api_login_id = false, $transaction_key = false ) {
		$this->api_login       = $api_login_id ?: ( defined( 'RTCL_AUTHNET_LOGIN' ) ? RTCL_AUTHNET_LOGIN : "" );
		$this->transaction_key = ( $transaction_key ?: ( defined( 'RTCL_AUTHNET_TRANSKEY' ) ? RTCL_AUTHNET_TRANSKEY : "" ) );
		$this->sandbox         = ( ! defined( 'RTCL_AUTHNET_SANDBOX' ) || RTCL_AUTHNET_SANDBOX );
		$this->url             = RTCL_AUTHNET_SANDBOX ? AuthNetEnv::SANDBOX : AuthNetEnv::PRODUCTION;
	}


	/**
	 * @return array[]|string|WP_Error
	 */
	public function getTransactionDetails() {

		if ( ! is_a( $this->transaction, TransactionType::class ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'Empty transaction object.', 'classified-listing-pro' ) );
		}
		$this->transaction->setRequestType( TransactionType::REQUEST_GET );
		$dataPayload = $this->transaction->getPayload();

		if ( is_wp_error( $dataPayload ) ) {
			return $dataPayload;
		}

		$payload = [
			'getTransactionDetailsRequest' => array_merge( $this->getMerchantAuthData(), $this->transaction->getPayload() )
		];
		//error_log( print_r( $payload, true ) );

		return $this->post_transaction( $payload );
	}

	/**
	 * @return array[]|string|WP_Error
	 */
	public function createTransaction() {

		if ( ! is_a( $this->transaction, TransactionType::class ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'Empty transaction object.', 'classified-listing-pro' ) );
		}
		$this->transaction->setRequestType( TransactionType::REQUEST_CREATE );

		$dataPayload = $this->transaction->getPayload();

		if ( is_wp_error( $dataPayload ) ) {
			return $dataPayload;
		}

		$payload = [
			'createTransactionRequest' => array_merge( $this->getMerchantAuthData(), $this->transaction->getPayload() )
		];
		//error_log( print_r( $payload, true ) );

		return $this->post_transaction( $payload );
	}

	/**
	 * @return array[]
	 */
	protected function getMerchantAuthData() {
		return [
			'merchantAuthentication' => [
				'name'           => Functions::clean( $this->api_login ),
				'transactionKey' => Functions::clean( $this->transaction_key ),
			]
		];
	}


	/**
	 * @return array[]|string|WP_Error
	 */
	public function createSubscription() {

		if ( ! is_a( $this->subscriptionType, SubscriptionType::class ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'Subscription type object.', 'classified-listing-pro' ) );
		}
		$this->subscriptionType->setRequestType( SubscriptionType::REQUEST_CREATE );

		$dataPayload = $this->subscriptionType->getPayload();

		if ( is_wp_error( $dataPayload ) ) {
			return $dataPayload;
		}

		$payload = [
			'ARBCreateSubscriptionRequest' => array_merge( $this->getMerchantAuthData(), $dataPayload )
		];

		return $this->post_subscription( $payload );
	}

	/**
	 * @return array[]|string|WP_Error
	 */
	public function createSubscriptionCp() {

		if ( ! is_a( $this->subscriptionType, SubscriptionType::class ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'Subscription type object.', 'classified-listing-pro' ) );
		}
		$this->subscriptionType->setRequestType( SubscriptionType::REQUEST_CREATE_CP );

		$dataPayload = $this->subscriptionType->getPayload();

		if ( is_wp_error( $dataPayload ) ) {
			return $dataPayload;
		}

		$payload = [
			'ARBCreateSubscriptionRequest' => array_merge( $this->getMerchantAuthData(), $dataPayload )
		];

		return $this->post_subscription( $payload );
	}


	/**
	 * @return array[]|string|WP_Error
	 */
	public function getSubscription() {

		if ( ! is_a( $this->subscriptionType, SubscriptionType::class ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'Subscription type object.', 'classified-listing-pro' ) );
		}
		$this->subscriptionType->setRequestType( SubscriptionType::REQUEST_GET );

		$dataPayload = $this->subscriptionType->getPayload();

		if ( is_wp_error( $dataPayload ) ) {
			return $dataPayload;
		}

		$payload = [
			'ARBGetSubscriptionRequest' => array_merge( $this->getMerchantAuthData(), $dataPayload )
		];

		return $this->post_subscription( $payload );
	}

	/**
	 * @return array[]|string|WP_Error
	 */
	public function cancelSubscription() {

		if ( ! is_a( $this->subscriptionType, SubscriptionType::class ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'Subscription type object.', 'classified-listing-pro' ) );
		}

		$this->subscriptionType->setRequestType( SubscriptionType::REQUEST_CANCEL );

		$dataPayload = $this->subscriptionType->getPayload();

		if ( is_wp_error( $dataPayload ) ) {
			return $dataPayload;
		}

		$payload = [
			'ARBCancelSubscriptionRequest' => array_merge( $this->getMerchantAuthData(), $dataPayload )
		];

		return $this->post_subscription( $payload );
	}

	/**
	 * @return array[]|string|WP_Error
	 */
	public function updateSubscription( $requestType = null ) {

		if ( ! is_a( $this->subscriptionType, SubscriptionType::class ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'Subscription type object.', 'classified-listing-pro' ) );
		}

		$this->subscriptionType->setRequestType( $requestType ?: SubscriptionType::REQUEST_UPDATE );

		$dataPayload = $this->subscriptionType->getPayload();

		if ( is_wp_error( $dataPayload ) ) {
			return $dataPayload;
		}

		$payload = [
			'ARBUpdateSubscriptionRequest' => array_merge( $this->getMerchantAuthData(), $dataPayload )
		];

		return $this->post_subscription( $payload );
	}

	/**
	 * Void function
	 *
	 * @param WC_Cardpay_Authnet_Gateway $gateway Gateway object.
	 * @param WC_Order                   $order   Order object.
	 * @param float                      $amount  Order amount.
	 *
	 * @return mixed
	 */
	public function void( $gateway, $order, $amount ) {
		$payload  = $this->get_payload( $gateway, $order, $amount, 'voidTransaction' );
		$response = $this->post_transaction( $payload );

		return $response;
	}

	/**
	 * Verify function
	 *
	 * @param WC_Cardpay_Authnet_Gateway $gateway Gateway object.
	 *
	 * @return mixed
	 */
	public function create_profile( $gateway ) {
		$payload  = $this->get_token_payload( $gateway );
		$response = $this->post_transaction( $payload );

		return $response;
	}

	/**
	 * Post_transaction function
	 *
	 * @param array $payload Payload json.
	 *
	 * @return string|WP_Error
	 */
	public function post_transaction( array $payload ) {
		$args = [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body'    => wp_json_encode( $payload ),
			'method'  => 'POST',
			'timeout' => 70,
		];

		$response = wp_remote_post( $this->url, $args );

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'There was a problem connecting to the payment gateway.', 'classified-listing-pro' ) );
		}

		$parsed_response = json_decode( preg_replace( '/\xEF\xBB\xBF/', '', $response['body'] ) );
		if ( ! empty( $parsed_response->messages->resultCode !== "Ok" ) ) {
			$error_msg = __( 'Transaction errors: ', 'classified-listing-pro' ) . $parsed_response->messages->message[0]->text;

			return new WP_Error( 'rtcl_authnet_error_' . $parsed_response->messages->message[0]->code, $error_msg );
		} else {
			return $parsed_response;
		}

	}


	/**
	 * Post_transaction function
	 *
	 * @param array $payload Payload json.
	 *
	 * @return string|WP_Error
	 */
	public function post_subscription( array $payload ) {
		$args = [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body'    => wp_json_encode( $payload ),
			'method'  => 'POST',
			'timeout' => 70,
		];

		$response = wp_remote_post( $this->url, $args );

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			return new WP_Error( 'rtcl_authnet_error', __( 'There was a problem connecting to the payment gateway.', 'classified-listing-pro' ) );
		}

		$parsed_response = json_decode( preg_replace( '/\xEF\xBB\xBF/', '', $response['body'] ) );

		if ( ! empty( $parsed_response->messages->resultCode !== "Ok" ) ) {
			$error_msg = __( 'Subscription errors: ', 'classified-listing-pro' ) . $parsed_response->messages->message[0]->text;

			return new WP_Error( 'rtcl_authnet_error', $error_msg );
		} else {
			return $parsed_response;
		}
	}

	/**
	 * Get_card_type function
	 *
	 * @param string $number Card number.
	 *
	 * @return string
	 */
	public function get_card_type( $number ) {
		if ( preg_match( '/^4\d{12}(\d{3})?(\d{3})?$/', $number ) ) {
			return 'Visa';
		} elseif ( preg_match( '/^3[47]\d{13}$/', $number ) ) {
			return 'American Express';
		} elseif ( preg_match( '/^(5[1-5]\d{4}|677189|222[1-9]\d{2}|22[3-9]\d{3}|2[3-6]\d{4}|27[01]\d{3}|2720\d{2})\d{10}$/', $number ) ) {
			return 'MasterCard';
		} elseif ( preg_match( '/^(6011|65\d{2}|64[4-9]\d)\d{12}|(62\d{14})$/', $number ) ) {
			return 'Discover';
		} elseif ( preg_match( '/^35(28|29|[3-8]\d)\d{12}$/', $number ) ) {
			return 'JCB';
		} elseif ( preg_match( '/^3(0[0-5]|[68]\d)\d{11}$/', $number ) ) {
			return 'Diners Club';
		}
	}

	/**
	 * @param bool $sandbox
	 */
	public function setSandbox( bool $sandbox ): void {
		$this->sandbox = $sandbox;
	}

	/**
	 * @param TransactionType $transaction
	 */
	public function setTransaction( TransactionType $transaction ): void {
		$this->transaction = $transaction;
	}

	public function setSubscriptionType( SubscriptionType $subscription ) {
		$this->subscriptionType = $subscription;
	}

}
