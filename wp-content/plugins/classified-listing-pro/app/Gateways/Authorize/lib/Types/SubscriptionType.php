<?php

namespace RtclPro\Gateways\Authorize\lib\Types;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use WP_Error;

class SubscriptionType {
	const REQUEST_CREATE = 'create';
	const REQUEST_CREATE_CP = 'create_cp';
	const REQUEST_UPDATE = 'update';
	const REQUEST_GET = 'get';
	const REQUEST_CANCEL = 'cancel';
	const REQUEST_UPDATE_PAYMENT = 'update_payment';
	private string $refId;
	private string $subscriptionId;
	private string $requestType;
	private bool $includeTransactions;

	private ProfileType $profileType;
	private Payment $order;
	private PaymentType $payment;
	private string $name;
	private PaymentScheduleType $paymentSchedule;
	private float $amount;
	private float $trialAmount;

	/**
	 * @param Payment $order
	 *
	 * @return SubscriptionType
	 */
	public function setOrder( Payment $order ) {
		$this->order = $order;

		return $this;
	}

	/**
	 * Sets a new refId
	 *
	 * @param string $refId
	 *
	 * @return self
	 */
	public function setRefId( $refId ) {
		$this->refId = $refId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRefId(): string {
		if ( empty( $this->refId ) ) {
			$this->refId = 'rtcl_' . time();
		}

		return $this->refId;
	}


	/**
	 * Gets as subscriptionId
	 *
	 * @return string
	 */
	public function getSubscriptionId() {
		return $this->subscriptionId;
	}

	/**
	 * Sets a new subscriptionId
	 *
	 * @param string $subscriptionId
	 *
	 * @return self
	 */
	public function setSubscriptionId( string $subscriptionId ): SubscriptionType {
		$this->subscriptionId = $subscriptionId;

		return $this;
	}

	/**
	 * Gets as name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets a new name
	 *
	 * @param string $name
	 *
	 * @return self
	 */
	public function setName( $name ) {
		$this->name = $name;

		return $this;
	}


	/**
	 * Gets as paymentSchedule
	 *
	 * @return PaymentScheduleType
	 */
	public function getPaymentSchedule() {
		return $this->paymentSchedule;
	}

	/**
	 * Sets a new paymentSchedule
	 *
	 * @param PaymentScheduleType $paymentSchedule
	 *
	 * @return self
	 */
	public function setPaymentSchedule( PaymentScheduleType $paymentSchedule ) {
		$this->paymentSchedule = $paymentSchedule;

		return $this;
	}

	/**
	 * Gets as amount
	 *
	 * @return float
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * Sets a new amount
	 *
	 * @param float $amount
	 *
	 * @return self
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;

		return $this;
	}


	/**
	 * Gets as trialAmount
	 *
	 * @return float
	 */
	public function getTrialAmount() {
		return $this->trialAmount;
	}

	/**
	 * Sets a new trialAmount
	 *
	 * @param float $trialAmount
	 *
	 * @return self
	 */
	public function setTrialAmount( $trialAmount ) {
		$this->trialAmount = $trialAmount;

		return $this;
	}

	/**
	 * Gets as payment
	 *
	 * @return PaymentType
	 */
	public function getPayment() {
		return $this->payment;
	}


	/**
	 * @param PaymentType $payment
	 *
	 * @return SubscriptionType
	 */
	public function setPayment( PaymentType $payment ): SubscriptionType {
		$this->payment = $payment;

		return $this;
	}

	/**
	 * @param ProfileType $profileType
	 *
	 * @return SubscriptionType
	 */
	public function setProfileType( ProfileType $profileType ): SubscriptionType {
		$this->profileType = $profileType;

		return $this;
	}


	public function getPayload() {
		$data = [ 'refId' => $this->getRefId() ?: ( $this->order ? Functions::clean( $this->order->get_id() ) : 'rtcl_' . time() ) ];
		if ( self::REQUEST_CREATE === $this->getRequestType() ) {
			if ( ! is_a( $this->order, Payment::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Order object can\'t be empty', 'classified-listing-pro' ) );
			}
			if ( ! is_a( $this->payment, PaymentType::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Payment object can\'t be empty', 'classified-listing-pro' ) );
			}
			if ( ! is_a( $this->paymentSchedule, PaymentScheduleType::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Payment object can\'t be empty', 'classified-listing-pro' ) );
			}
			$user_info        = get_userdata( $this->order->get_customer_id() );
			$first_name       = $this->order->get_billing_first_name() ?: $user_info->user_login;
			$lase_name        = $this->order->get_billing_last_name() ?: $user_info->ID;
			$billing_address  = $this->order->get_billing_address_1();
			$billing_city     = $this->order->get_billing_city();
			$billing_state    = $this->order->get_billing_state();
			$billing_postcode = $this->order->get_billing_postcode();
			$billing_country  = $this->order->get_billing_country();
			$billing_email    = $this->order->get_billing_email();
			$creditCard       = $this->payment->getCreditCard();
			$data             = [
				'subscription' => [
					'name'            => $this->getName(),
					"paymentSchedule" => [
						"interval"         => [
							"length" => $this->paymentSchedule->getInterval()->getLength(),
							"unit"   => $this->paymentSchedule->getInterval()->getUnit()
						],
						"startDate"        => $this->paymentSchedule->getStartDate()->format( "Y-m-d" ),
						"totalOccurrences" => $this->paymentSchedule->getTotalOccurrences(),
						"trialOccurrences" => $this->paymentSchedule->getTrialOccurrences()
					],
					"amount"          => $this->getAmount(),
					"trialAmount"     => $this->getTrialAmount(),
					"payment"         => [
						"creditCard" => [
							'cardNumber'     => $creditCard->getCardNumber(),
							'expirationDate' => $creditCard->getExpirationDate(),
							'cardCode'       => $creditCard->getCardCode(),
						]
					],
					"order"           => [
						"invoiceNumber" => $this->order->get_id(),
						"description"   => $this->order->pricing->getTitle()
					],
					"customer"        => [
						'type'  => 'individual',
						'id'    => uniqid(),
						'email' => Functions::clean( $billing_email ?: $this->order->get_customer_email() ),
					],
					"billTo"          => [
						'firstName' => Functions::clean( substr( $first_name, 0, 50 ) ),
						'lastName'  => Functions::clean( substr( $lase_name, 0, 50 ) ),
						'address'   => Functions::clean( substr( $billing_address, 0, 30 ) ),
						'city'      => Functions::clean( substr( $billing_city, 0, 40 ) ),
						'state'     => Functions::clean( substr( $billing_state, 0, 40 ) ),
						'zip'       => Functions::clean( substr( $billing_postcode, 0, 10 ) ),
						'country'   => Functions::clean( substr( $billing_country, 0, 60 ) ),
					]
				]
			];
		} elseif ( self::REQUEST_CREATE_CP === $this->getRequestType() ) {
			if ( ! is_a( $this->order, Payment::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Order object can\'t be empty', 'classified-listing-pro' ) );
			}
			if ( ! is_a( $this->profileType, ProfileType::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Payment object can\'t be empty', 'classified-listing-pro' ) );
			}
			if ( ! is_a( $this->paymentSchedule, PaymentScheduleType::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Payment object can\'t be empty', 'classified-listing-pro' ) );
			}

			$data = [
				'subscription' => [
					'name'            => $this->getName(),
					"paymentSchedule" => [
						"interval"         => [
							"length" => $this->paymentSchedule->getInterval()->getLength(),
							"unit"   => $this->paymentSchedule->getInterval()->getUnit()
						],
						"startDate"        => $this->paymentSchedule->getStartDate()->format( "Y-m-d" ),
						"totalOccurrences" => $this->paymentSchedule->getTotalOccurrences(),
						"trialOccurrences" => $this->paymentSchedule->getTrialOccurrences()
					],
					"amount"          => $this->getAmount(),
					"trialAmount"     => $this->getTrialAmount(),
					"order"           => [
						"invoiceNumber" => $this->order->get_id(),
						"description"   => $this->order->pricing->getTitle()
					],
					"profile"         => [
						'customerProfileId'        => $this->profileType->getCustomerProfileId(),
						'customerPaymentProfileId' => $this->profileType->getCustomerPaymentProfileId()
					],
				]
			];
		} elseif ( self::REQUEST_GET === $this->getRequestType() ) {
			$data["subscriptionId"] = $this->getSubscriptionId();
			if ( ! empty( $this->includeTransactions ) ) {
				$data['includeTransactions'] = $this->includeTransactions;
			}

		} elseif ( self::REQUEST_CANCEL === $this->getRequestType() ) {
			$data["subscriptionId"] = $this->getSubscriptionId();
		} elseif ( self::REQUEST_UPDATE_PAYMENT === $this->getRequestType() ) {
			if ( ! is_a( $this->payment, PaymentType::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Payment object can\'t be empty', 'classified-listing-pro' ) );
			}
			$data["subscriptionId"] = $this->getSubscriptionId();
			if ( ! empty( $this->payment ) ) {
				$creditCard                      = $this->payment->getCreditCard();
				$data["subscription"]["payment"] = [
					"creditCard" => [
						'cardNumber'     => $creditCard->getCardNumber(),
						'expirationDate' => $creditCard->getExpirationDate(),
						'cardCode'       => $creditCard->getCardCode(),
					]
				];
			}
		}

		return apply_filters( 'rtcl_auth_net_payload_' . $this->getRequestType() . '_subscription', $data, $this );
	}

	/**
	 * @param string $requestType
	 *
	 * @return self
	 */
	public function setRequestType( string $requestType ): SubscriptionType {
		$this->requestType = $requestType;

		return $this;
	}

	public function getRequestType(): string {
		return $this->requestType;
	}

	/**
	 * @param bool $includeTransactions
	 *
	 * @return SubscriptionType
	 */
	public function setIncludeTransactions( bool $includeTransactions ): SubscriptionType {
		$this->includeTransactions = $includeTransactions;

		return $this;
	}
}