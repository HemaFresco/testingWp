<?php

namespace RtclPro\Gateways\Authorize;


use DateTime;
use Exception;
use Rtcl\Helpers\Functions;
use Rtcl\Log\Logger;
use Rtcl\Models\Payment;
use Rtcl\Models\PaymentGateway;
use RtclPro\Gateways\Authorize\lib\AuthNetAPI;
use RtclPro\Gateways\Authorize\lib\AuthNetWebhook;
use RtclPro\Gateways\Authorize\lib\Types\CreditCardType;
use RtclPro\Gateways\Authorize\lib\Types\IntervalAType;
use RtclPro\Gateways\Authorize\lib\Types\OrderType;
use RtclPro\Gateways\Authorize\lib\Types\PaymentScheduleType;
use RtclPro\Gateways\Authorize\lib\Types\PaymentType;
use RtclPro\Gateways\Authorize\lib\Types\ProfileType;
use RtclPro\Gateways\Authorize\lib\Types\SubscriptionType;
use RtclPro\Gateways\Authorize\lib\Types\TransactionType;
use RtclPro\Models\Subscription;
use RtclPro\Models\Subscriptions;
use WP_Error;

class GatewayAuthorize extends PaymentGateway {

	/**
	 * @var string
	 */
	private $authorizenet_cardtypes;
	private $authorizenet_description;
	private $authorizenet_apilogin;
	private $authorizenet_transactionkey;
	private $authorizenet_sandbox;
	private $authorizenet_authorize_only;
	private $authorizenet_meta_cartspan;
	//https://github.dev/stymiee/authnetjson
	//1EBA3122D088FE2A837F8C9DDC544CE46626D3CB769950BFFFC6D87578BCA0B3A861817F85A6E61CDF930EF14DEFF2B141D2351C1B3F2568D3C8AC573BED4895
	/**
	 * @var string
	 */
	private $signature_key;

	public function __construct() {
		$this->id           = 'authorizenet';
		$this->option       = $this->option . $this->id;
		$this->icon         = plugins_url( 'images/authorizenet.png', __FILE__ );
		$this->has_fields   = true;
		$this->method_title = 'Authorize.Net Cards Settings';
		$this->init_form_fields();
		$this->init_settings();
		$this->supports                 = [ 'products', 'refunds' ];
		$this->authorizenet_description = $this->get_option( 'authorizenet_description' );

		$this->title                       = $this->get_option( 'authorizenet_title' );
		$this->authorizenet_apilogin       = $this->get_option( 'authorizenet_apilogin' ); // "43j733Z8wKz";//
		$this->authorizenet_transactionkey = $this->get_option( 'authorizenet_transactionkey' ); // 5329wuCMF2FDY8ga
		$this->authorizenet_sandbox        = $this->get_option( 'authorizenet_sandbox' );
		$this->authorizenet_authorize_only = $this->get_option( 'authorizenet_authorize_only' );
		$authorizenet_card_types           = $this->get_option( 'authorizenet_cardtypes' );
		$this->authorizenet_cardtypes      = is_array( $authorizenet_card_types ) && ! empty( $authorizenet_card_types ) ? $authorizenet_card_types : [];
		$this->authorizenet_meta_cartspan  = $this->get_option( 'authorizenet_meta_cartspan' );
		$this->signature_key               = $this->get_option( 'signature_key' );
		defined( 'RTCL_AUTHNET_LOGIN' ) || define( 'RTCL_AUTHNET_LOGIN', $this->authorizenet_apilogin );
		defined( 'RTCL_AUTHNET_TRANSKEY' ) || define( 'RTCL_AUTHNET_TRANSKEY', $this->authorizenet_transactionkey );
		defined( 'RTCL_AUTHNET_SIGNATURE' ) || define( 'RTCL_AUTHNET_SIGNATURE', $this->signature_key );
		defined( 'RTCL_AUTHNET_SANDBOX' ) || define( 'RTCL_AUTHNET_SANDBOX', 'yes' === $this->authorizenet_sandbox );
		defined( 'RTCL_AUTHNET_AUTHORIZE_ONLY' ) || define( 'RTCL_AUTHNET_AUTHORIZE_ONLY', 'yes' === $this->authorizenet_authorize_only );
		add_action( 'rtcl_api_rtcl_gateway_' . $this->id, [ $this, 'webhook' ] );

	}

	public function webhook() {
		$webHook = new AuthNetWebhook( $this );
		$webHook->check_for_webhook();
	}

	public function getSignatureKey() {
		return $this->signature_key;
	}

	public function init_form_fields() {

		$this->form_fields = [
			'enabled'                     => [
				'title' => esc_html__( 'Enable/Disable', 'classified-listing-pro' ),
				'type'  => 'checkbox',
				'label' => esc_html__( 'Enable Authorize.Net', 'classified-listing-pro' ),
			],
			'authorizenet_title'          => [
				'title'       => esc_html__( 'Title', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This controls the title which the buyer sees during checkout.', 'classified-listing-pro' ),
				'default'     => esc_html__( 'Authorize.Net', 'classified-listing-pro' ),
			],
			'authorizenet_description'    => [
				'title'       => esc_html__( 'Description', 'classified-listing-pro' ),
				'type'        => 'textarea',
				'description' => esc_html__( 'This controls the description which the user sees during checkout.', 'classified-listing-pro' ),
				'default'     => esc_html__( 'All cards are charged by &copy;Authorize.Net &#174;&#8482; servers.', 'classified-listing-pro' ),
			],
			'authorizenet_apilogin'       => [
				'title'       => esc_html__( 'API Login ID', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This is the API Login ID Authorize.net.', 'classified-listing-pro' ),
				'default'     => '',
				'placeholder' => esc_html__( 'Authorize.Net API Login ID', 'classified-listing-pro' )
			],
			'authorizenet_transactionkey' => [
				'title'       => esc_html__( 'Transaction Key', 'classified-listing-pro' ),
				'type'        => 'text',
				'description' => esc_html__( 'This is the Transaction Key of Authorize.Net.', 'classified-listing-pro' ),
				'default'     => '',
				'placeholder' => esc_html__( 'Authorize.Net Transaction Key', 'classified-listing-pro' )
			],
			'authorizenet_sandbox'        => [
				'title'       => esc_html__( 'Authorize.Net sandbox', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable Authorize.Net sandbox (Live Mode if Unchecked)', 'classified-listing-pro' ),
				'description' => esc_html__( 'If checked its in sandbox mode and if unchecked its in live mode', 'classified-listing-pro' ),
				'default'     => 'no'
			],
			'authorizenet_authorize_only' => [
				'title'       => esc_html__( 'Authorize Only', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Authorize Only Mode (Authorize & Capture If Unchecked).<span style="color:red;">Make sure to keep <b>Unchecked</b> if your Address Verification Service (AVS) is set to hold transaction for review.</span>', 'classified-listing-pro' ),
				'description' => esc_html__( 'If checked will only authorize the credit card only upon checkout.', 'classified-listing-pro' ),
				'default'     => 'no',
			],
			'authorizenet_meta_cartspan'  => [
				'title'       => esc_html__( 'Authorize.Net + Cartspan', 'classified-listing-pro' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable Authorize.Net Metas for Cartspan', 'classified-listing-pro' ),
				'description' => esc_html__( 'If checked will store last4 and card brand in local db from Transaction response', 'classified-listing-pro' ),
				'default'     => 'no',
			],
			'authorizenet_cardtypes'      => [
				'title'    => esc_html__( 'Accepted Cards', 'classified-listing-pro' ),
				'type'     => 'multiselect',
				'class'    => 'rtcl-select2',
				'css'      => 'width: 350px;',
				'desc_tip' => esc_html__( 'Select the card types to accept.', 'classified-listing-pro' ),
				'options'  => [
					'mastercard' => 'MasterCard',
					'visa'       => 'Visa',
					'discover'   => 'Discover',
					'amex'       => 'American Express',
					'jcb'        => 'JCB',
					'dinersclub' => 'Dinners Club',
				],
				'default'  => [ 'mastercard', 'visa', 'discover', 'amex' ],
			],
			'webhook_url'                 => [
				'type'        => 'html',
				'title'       => __( 'Webhook url', 'classified-listing-pro' ),
				'html'        => '<strong>' . rtcl()->api_request_url( 'rtcl_gateway_' . $this->id ) . '</strong>',
				'description' => sprintf( __( '<strong>Important:</strong> the webhook url is called by Authorize.net when events occur in your account, like a source becomes chargeable. You must add this webhook to your Stripe Dashboard if you are using any of the local gateways. %1$sWebhook guide%2$s', 'classified-listing-pro' ), '<a target="_blank" href="https://sandbox.authorize.net/help/Merchant_Interface_RoboHelp_Project.htm#Account/Settings/Security_Settings/General_Settings/Webhooks.htms">', '</a>' ),
			],
			'signature_key'               => [
				'type'        => 'text',
				'title'       => __( 'Signature Key', 'classified-listing-pro' ),
				'description' => sprintf( __( 'Signature Key. %1$sWebhook guide%2$s', 'classified-listing-pro' ), '<a target="_blank" href="https://sandbox.authorize.net/help/Merchant_Interface_RoboHelp_Project.htm#Account/Settings/Security_Settings/General_Settings/Webhooks.htms">', '</a>' ),
			],
		];
	}

	public function payment_fields(): string {
		$html = null;
		$html .= apply_filters( 'rtcl_authorizenet_description', wpautop( wp_kses_post( wptexturize( trim( $this->authorizenet_description ) ) ) ) );
		$html .= $this->form();

		return $html;
	}

	public function field_name( $name ): string {
		return $this->supports( 'tokenization' ) ? '' : ' name="' . esc_attr( $this->id . '-' . $name ) . '" ';
	}

	public function form() {
		$this->load_stripe_scripts();

		ob_start();
		?>

		<fieldset id="wc-<?php echo esc_attr( $this->id ); ?>-cc-form" class='rtcl-credit-card-form rtcl-payment-form'>
			<?php do_action( 'rtcl_credit_card_form_start', $this->id ); ?>
			<div class="form-group">
				<label
					for="<?php echo esc_attr( $this->id ) ?>-card-number"><?php esc_html_e( 'Card Number', 'classified-listing-pro' ) ?>
					<span class="required">*</span></label>
				<input id="<?php echo esc_attr( $this->id ) ?>-card-number"
					   class="input-text rtcl-credit-card-number form-control" type="text" maxlength="20"
					   autocomplete="off"
					   placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" <?php echo $this->field_name( 'card-number' ) ?> />
			</div>
			<div class="form-row">
				<div class="col form-group">
					<label
						for="<?php echo esc_attr( $this->id ) ?>-card-expiry"><?php esc_html_e( 'Expiry (MM/YY)', 'classified-listing-pro' ) ?>
						<span class="required">*</span></label>
					<input id="<?php echo esc_attr( $this->id ) ?>-card-expiry"
						   class="input-text rtcl-credit-card-expiry form-control" type="text" autocomplete="off"
						   placeholder="<?php esc_attr_e( 'MM / YY', 'classified-listing-pro' ) ?>" <?php echo $this->field_name( 'card-expiry' ) ?> />
				</div>
				<div class="col form-group">
					<label
						for="<?php echo esc_attr( $this->id ) ?>-card-cvc"><?php esc_html_e( 'Card Code', 'classified-listing-pro' ) ?>
						<span class="required">*</span></label>
					<input id="<?php echo esc_attr( $this->id ) ?>-card-cvc"
						   class="input-text rtcl-credit-card-cvc form-control" type="text" autocomplete="off"
						   placeholder="<?php esc_attr_e( 'CVC', 'classified-listing-pro' ) ?>" <?php echo $this->field_name( 'card-cvc' ) ?> />
				</div>
			</div>
			<?php do_action( 'rtcl_credit_card_form_end', $this->id ); ?>
			<div class="clear"></div>
		</fieldset>
		<?php
		return ob_get_clean();
	}

	public function process_payment( $order, $data = [] ) {
		$message  = null;
		$result   = 'error';
		$redirect = null;
		if ( ! $order instanceof Payment ) {
			return [
				'result'   => $result,
				'message'  => esc_html__( 'Payment not found', 'classified-listing-pro' ),
				'redirect' => $redirect,
			];
		}
		$log      = new Logger();
		$card_num = '';
		if ( ! empty( $data['number'] ) ) {
			$card_num = sanitize_text_field( str_replace( ' ', '', $data['number'] ) );
		} else if ( ! empty( $_POST['authorizenet-card-number'] ) ) {
			$card_num = sanitize_text_field( str_replace( ' ', '', $_POST['authorizenet-card-number'] ) );
		}
		$cardtype = $this->get_card_type( $card_num );

		if ( ! in_array( $cardtype, $this->authorizenet_cardtypes ) ) {
			$log->info( 'Merchant do not support accepting in ' . $cardtype );
			$message = sprintf( esc_html__( 'Merchant do not support accepting in %s', 'classified-listing-pro' ), $cardtype );

			return [
				'result'   => $result,
				'message'  => $message,
				'redirect' => $redirect,
			];
		}

		$exp_year = $exp_date = $exp_month = null;
		if ( isset( $_POST['authorizenet-card-expiry'] ) ) {
			$exp_date = trim( $_POST['authorizenet-card-expiry'] );
			if ( $exp_date && strpos( $exp_date, '/' ) !== false ) {
				$exp_date  = explode( "/", sanitize_text_field( $_POST['authorizenet-card-expiry'] ) );
				$exp_month = ! empty( $exp_date[0] ) ? trim( $exp_date[0] ) : null;
				$exp_year  = ! empty( $exp_date[1] ) ? trim( $exp_date[1] ) : null;
			} else if ( $exp_date && strpos( $exp_date, '-' ) !== false ) {
				$exp_date  = explode( "-", sanitize_text_field( $_POST['authorizenet-card-expiry'] ) );
				$exp_month = ! empty( $exp_date[0] ) ? trim( $exp_date[0] ) : null;
				$exp_year  = ! empty( $exp_date[1] ) ? trim( $exp_date[1] ) : null;
			}
		}
		$exp_year  = ! empty( $data['exp_year'] ) ? sanitize_text_field( $data['exp_year'] ) : $exp_year;
		$exp_month = ! empty( $data['exp_month'] ) ? sanitize_text_field( $data['exp_month'] ) : $exp_month;
		if ( $exp_year && strlen( $exp_year ) == 2 ) {
			$exp_year += 2000;
		}
		if ( $exp_year && $exp_month ) {
			$exp_date = $exp_year . '-' . $exp_month;
		}

		$cvc = ! empty( $data['cvc'] ) ? sanitize_text_field( $data['cvc'] ) : ( isset( $_POST['authorizenet-card-cvc'] ) ? sanitize_text_field( $_POST['authorizenet-card-cvc'] ) : '' );
		if ( ! $exp_date || ! $cvc ) {
			return [
				'result'   => $result,
				'message'  => esc_html__( 'Card expired month or year / CVC is not defined.', 'classified-listing-pro' ),
				'redirect' => $redirect,
			];
		}

		try {
			$authNet     = new AuthNetAPI();
			$transaction = new TransactionType();
			$transaction->setTransactionType( RTCL_AUTHNET_AUTHORIZE_ONLY ? "authOnlyTransaction" : 'authCaptureTransaction' );


			$creditCard = new CreditCardType();
			$creditCard->setCardNumber( $card_num );
			$creditCard->setExpirationDate( $exp_date );
			$creditCard->setCardCode( $cvc );

			$payment = new PaymentType();
			$payment->setCreditCard( $creditCard );
			$enableSubscription = $order->is_membership() && Functions::get_option_item( 'rtcl_payment_settings', 'subscription', false, 'checkbox' );

			$orderType = new OrderType();
			$orderType->setInvoiceNumber( $order->get_order_number() );
			$orderType->setDescription( $order->pricing->getTitle() );
			$transaction->setOrderType( $orderType );
			$transaction->setOrder( $order );
			$transaction->setPayment( $payment );

			$authNet->setTransaction( $transaction );
			$response = $authNet->createTransaction();
			if ( is_wp_error( $response ) ) {
				$order->add_note( $response->get_error_message() );
				throw new Exception( $response->get_error_message() );
			}
			if ( isset( $response->transactionResponse->responseCode ) && '1' === $response->transactionResponse->responseCode ) {
				$trans_id = $response->transactionResponse->transId;
				$order->payment_complete( $trans_id );
				$amount          = $order->get_total();
				$amount_approved = number_format( $amount, '2', '.', '' );
				$message         = RTCL_AUTHNET_AUTHORIZE_ONLY ? 'authorized' : 'completed';
				$order->add_note(
					sprintf(
						__( "Authorize.Net payment %1\$s for %2\$s. Transaction ID: %3\$s.\n\n <strong>AVS Response:</strong> %4\$s.\n\n <strong>CVV2 Response:</strong> %5\$s.", 'classified-listing-pro' ),
						$message,
						$amount_approved,
						$response->transactionResponse->transId,
						$this->get_avs_message( $response->transactionResponse->avsResultCode ),
						$this->get_cvv_message( $response->transactionResponse->cvvResultCode )
					)
				);
				$transMeta = [
					'transId'        => $response->transactionResponse->transId,
					'accountNumber'  => substr( $response->transactionResponse->accountNumber, - 4 ),
					'expirationDate' => $exp_date,
					'transType'      => $transaction->getTransactionType(),
					'accountType'    => $response->transactionResponse->accountType
				];
				if ( $response->profileResponse->messages->resultCode === 'Ok' ) {
					$transMeta['profile'] = [
						'customerProfileId'        => $response->profileResponse->customerProfileId,
						'customerPaymentProfileId' => $response->profileResponse->customerPaymentProfileIdList[0]
					];
				}
				add_post_meta( $order->get_id(), '_' . $this->id . '_transaction', $transMeta );
				if ( $enableSubscription && $response->profileResponse->messages->resultCode === 'Ok' ) {
					$subIn = ( new Subscriptions() )->findOneByUserId( $order->get_customer_id() );
					if ( $subIn && $subIn->getGatewayId() === $this->id ) {
						// Creating the API Request with required parameters
						$subscription = new SubscriptionType();
						$subscription->setRefId( $order->get_id() );
						$subscription->setSubscriptionId( $subIn->getSubId() );
						$authNet->setSubscriptionType( $subscription );
						$subResponse = $authNet->getSubscription();
						if ( is_wp_error( $subResponse ) ) {
							$log->info( 'Authorized webhook: ' . $subResponse->get_error_message() );
						}
						$subResponse = $authNet->cancelSubscription();
						if ( is_wp_error( $subResponse ) ) {
							$log->info( 'Authorized webhook: ' . $subResponse->get_error_message() );
						}
						$log->info( 'Payment Gateway id: ' . $this->id . ' : ' . "SUCCESS : " . $subResponse->messages->message[0]->code . "  " . $subResponse->messages->message[0]->text );
						$subIn->updateStatus( Subscription::STATUS_CANCELED );
					}
					$subscription = new SubscriptionType();
					$subscription->setRefId( $order->get_id() );
					$subscription->setName( $order->pricing->getTitle() );
					$subscription->setOrder( $order );
					$interval = new IntervalAType();
					$interval->setLength( $order->pricing->getVisible() );
					$interval->setUnit( "days" );

					$paymentSchedule = new PaymentScheduleType();
					$paymentSchedule->setInterval( $interval );
					$startDate = new DateTime();
					$startDate->modify( '+' . $order->pricing->getVisible() . ' day' );
					$paymentSchedule->setStartDate( $startDate );
					$paymentSchedule->setTotalOccurrences( "9999" );
					$paymentSchedule->setTrialOccurrences( "0" );
					$subscription->setPaymentSchedule( $paymentSchedule );
					$chargeAmount = number_format( $order->get_total(), 2, '.', '' );
					$subscription->setAmount( $chargeAmount );
					$subscription->setTrialAmount( '0.00' );
					$profile = new ProfileType();
					$profile->setCustomerProfileId( $response->profileResponse->customerProfileId );
					$profile->setCustomerPaymentProfileId( $response->profileResponse->customerPaymentProfileIdList[0] );
					$subscription->setProfileType( $profile );
					$authNet->setSubscriptionType( $subscription );
					$subResponse = $authNet->createSubscriptionCp();

					if ( is_wp_error( $subResponse ) ) {
						$order->add_note( $subResponse->get_error_message() );
					} else {
						$subscriptionIn = ( new Subscriptions() )->create( [
							'sub_id'     => $subResponse->subscriptionId,
							'gateway_id' => $this->id,
							'status'     => Subscription::STATUS_ACTIVE,
							'product_id' => $order->pricing->getId(),
							'occurrence' => 1,
							'expiry_at'  => $paymentSchedule->getStartDate()->format( 'Y-m-d H:i:s' ),
							'price'      => $order->get_total(),
							'name'       => $order->pricing->getTitle(),
							'user_id'    => $order->get_customer_id()
						] );

						if ( is_wp_error( $subscriptionIn ) ) {
							throw new Exception( $subscriptionIn->get_error_message() );
						}
						$subscriptionIn->update_meta( 'profile', $subResponse->profile );
						$subscriptionIn->update_meta( 'cc', [
							'type'   => $response->transactionResponse->accountType,
							'last4'  => substr( $response->transactionResponse->accountNumber, - 4 ),
							'expiry' => $transMeta['expirationDate']
						] );
					}
				}


				// Return thank you redirect.
				return [
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				];
			} else {
				$order->add_note( __( 'Payment error: Please check your credit card details and try again.', 'classified-listing-pro' ) );

				throw new Exception( __( 'Payment error: Please check your credit card details and try again.', 'classified-listing-pro' ) );
			}
		} catch ( Exception $e ) {

			error_log( 'Throw ' . $e->getMessage() );

			return [
				'result'   => $result,
				'message'  => $e->getMessage(),
				'redirect' => $redirect,
			];

		}

	}

	/**
	 * Get Icon
	 */
	public function get_icon() {
		$icon = '';
		if ( is_array( $this->authorizenet_cardtypes ) ) {
			foreach ( $this->authorizenet_cardtypes as $card_type ) {

				if ( $url = $this->get_payment_method_image_url( $card_type ) ) {

					$icon .= '<img width="45" src="' . esc_url( $url ) . '" alt="' . esc_attr( strtolower( $card_type ) ) . '" />';
				}
			}
		} else {
			$icon .= '<img src="' . esc_url( plugins_url( 'images/authorizenet.png', __FILE__ ) ) . '" alt="Authorize.Net Payment Gateway" />';
		}

		return apply_filters( 'rtcl_authorizenet_icon', $icon, $this->id );
	}

	public function get_payment_method_image_url( $type ) {

		$image_type = strtolower( $type );

		return plugins_url( 'images/' . $image_type . '.png', __FILE__ );
	}

	/**
	 * Get Icon
	 */
	public function load_stripe_scripts() {
		wp_enqueue_script( 'rtcl-credit-card-form' );
	}

	/**
	 * Get Card Types
	 */
	function get_card_type( $number ) {

		$number = preg_replace( '/[^\d]/', '', $number );
		if ( preg_match( '/^3[47][0-9]{13}$/', $number ) ) {
			return 'amex';
		} elseif ( preg_match( '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $number ) ) {
			return 'dinersclub';
		} elseif ( preg_match( '/^6(?:011|5[0-9][0-9])[0-9]{12}$/', $number ) ) {
			return 'discover';
		} elseif ( preg_match( '/^(?:2131|1800|35\d{3})\d{11}$/', $number ) ) {
			return 'jcb';
		} elseif ( preg_match( '/^5[1-5][0-9]{14}$/', $number ) ) {
			return 'mastercard';
		} elseif ( preg_match( '/^4[0-9]{12}(?:[0-9]{3})?$/', $number ) ) {
			return 'visa';
		} else {
			return 'unknown card';
		}
	}

	/**
	 * Function to check IP
	 *
	 * @return array|false|string
	 */
	function get_client_ip() {
		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ipaddress = getenv( 'HTTP_CLIENT_IP' );
		} else if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' );
		} else if ( getenv( 'HTTP_X_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED' );
		} else if ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
		} else if ( getenv( 'HTTP_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED' );
		} else if ( getenv( 'REMOTE_ADDR' ) ) {
			$ipaddress = getenv( 'REMOTE_ADDR' );
		} else {
			$ipaddress = '0.0.0.0';
		}

		return $ipaddress;
	}


	/**
	 * Get_avs_message function.
	 *
	 * @access public
	 *
	 * @param string $code AVS code.
	 *
	 * @return string
	 */
	public function get_avs_message( $code ) {
		$avs_messages = [
			'A' => __( 'Street Address: Match -- First 5 Digits of ZIP: No Match', 'classified0listing-pro' ),
			'B' => __( 'Address not provided for AVS check or street address match, postal code could not be verified', 'classified0listing-pro' ),
			'E' => __( 'AVS Error', 'classified0listing-pro' ),
			'G' => __( 'Non U.S. Card Issuing Bank', 'classified0listing-pro' ),
			'N' => __( 'Street Address: No Match -- First 5 Digits of ZIP: No Match', 'classified0listing-pro' ),
			'P' => __( 'AVS not applicable for this transaction', 'classified0listing-pro' ),
			'R' => __( 'Retry, System Is Unavailable', 'classified0listing-pro' ),
			'S' => __( 'AVS Not Supported by Card Issuing Bank', 'classified0listing-pro' ),
			'U' => __( 'Address Information For This Cardholder Is Unavailable', 'classified0listing-pro' ),
			'W' => __( 'Street Address: No Match -- All 9 Digits of ZIP: Match', 'classified0listing-pro' ),
			'X' => __( 'Street Address: Match -- All 9 Digits of ZIP: Match', 'classified0listing-pro' ),
			'Y' => __( 'Street Address: Match - First 5 Digits of ZIP: Match', 'classified0listing-pro' ),
			'Z' => __( 'Street Address: No Match - First 5 Digits of ZIP: Match', 'classified0listing-pro' ),
		];
		if ( array_key_exists( $code, $avs_messages ) ) {
			return $avs_messages[ $code ];
		} else {
			return '';
		}
	}

	/**
	 * Get_cvv_message function.
	 *
	 * @access public
	 *
	 * @param string $code CVV code.
	 *
	 * @return string
	 */
	public function get_cvv_message( $code ) {
		$cvv_messages = [
			'M' => __( 'CVV2/CVC2 Match', 'woocommerce-cardpay-authnet' ),
			'N' => __( 'CVV2 / CVC2 No Match', 'woocommerce-cardpay-authnet' ),
			'P' => __( 'Not Processed', 'woocommerce-cardpay-authnet' ),
			'S' => __( 'Merchant Has Indicated that CVV2 / CVC2 is not present on card', 'woocommerce-cardpay-authnet' ),
			'U' => __( 'Issuer is not certified and/or has not provided visa encryption keys', 'woocommerce-cardpay-authnet' ),
		];
		if ( array_key_exists( $code, $cvv_messages ) ) {
			return $cvv_messages[ $code ];
		} else {
			return '';
		}
	}

	/**
	 * @param Subscription|int $subscription
	 *
	 * @return bool|WP_Error
	 */
	public function cancelSubscription( $subscription ) {
		$_subscription = null;
		if ( is_numeric( $subscription ) ) {
			$_subscription = ( new Subscriptions() )->findById( $subscription );
		} elseif ( is_a( $subscription, Subscription::class ) ) {
			$_subscription = $subscription;
		}

		if ( ! $_subscription ) {
			return new WP_Error( 'rtcl_authnet_subscription_cancel', __( 'No subscription found to remove', 'classified-listing-pro' ) );
		}

		if ( Subscription::STATUS_ACTIVE !== $_subscription->getStatus() ) {
			return new WP_Error( 'rtcl_authnet_subscription_cancel', __( 'This subscription is not active.', 'classified-listing-pro' ) );
		}
		$authNet      = new AuthNetAPI();
		$subscription = new SubscriptionType();
		$subscription->setSubscriptionId( $_subscription->getSubId() );
		$authNet->setSubscriptionType( $subscription );
		$response = $authNet->cancelSubscription();
		if ( ! is_wp_error( $response ) ) {
			$_subscription->updateStatus( Subscription::STATUS_CANCELED );

			return true;
		}

		return $response;
	}


	/**
	 * @param Subscription $subscription
	 * @param PaymentType  $paymentType
	 *
	 * @return array|WP_Error
	 */
	public function updateSubscriptionPayment( Subscription $subscription, PaymentType $paymentType ) {
		$authNet          = new AuthNetAPI();
		$subscriptionType = new SubscriptionType();
		$subscriptionType->setSubscriptionId( $subscription->getSubId() );
		$subscriptionType->setPayment( $paymentType );
		$authNet->setSubscriptionType( $subscriptionType );

		$response = $authNet->updateSubscription( SubscriptionType::REQUEST_UPDATE_PAYMENT );
		if ( ! is_wp_error( $response ) ) {
			$data = [
				'cc' => [
					'type'   => $this->get_card_type( $paymentType->getCreditCard()->getCardNumber() ),
					'last4'  => substr( $paymentType->getCreditCard()->getCardNumber(), - 4 ),
					'expiry' => $paymentType->getCreditCard()->getExpirationDate()
				]
			];
			$subscription->updatePayment( $data );

			return $data;
		}

		return $response;
	}

	/**
	 * @return array|string
	 */
	public function getAuthorizenetCardTypes() {
		return $this->authorizenet_cardtypes;
	}
}