<?php

namespace RtclPro\Gateways\Authorize\lib\Types;

use Automattic\WooCommerce\Admin\Overrides\Order;
use Exception;
use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use WP_Error;

class TransactionType {
	const REQUEST_GET = 'get';
	const REQUEST_CREATE = 'create';
	private string $transactionType;
	private PaymentType $payment;
	private OrderType $orderType;
	private Payment $order;
	private string $requestType;
	private string $transId;
	private string $refId;

	/**
	 * @param string $transactionType
	 *
	 * @return TransactionType
	 */
	public function setTransactionType( string $transactionType ): TransactionType {
		$this->transactionType = $transactionType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTransactionType(): string {
		return $this->transactionType;
	}

	/**
	 * @param OrderType $orderType
	 *
	 * @return TransactionType
	 */
	public function setOrderType( OrderType $orderType ): TransactionType {
		$this->orderType = $orderType;

		return $this;
	}

	/**
	 * @param Payment $order
	 *
	 * @return TransactionType
	 */
	public function setOrder( Payment $order ): TransactionType {
		$this->order = $order;

		return $this;
	}

	/**
	 * @param CreditCardType $creditCard
	 */
	public function setCreditCard( CreditCardType $creditCard ): void {
		$this->creditCard = $creditCard;
	}


	/**
	 * @return array[]|WP_Error
	 */
	public function getPayload() {
		$data = [ 'refId' => $this->getRefId() ];
		if ( self::REQUEST_CREATE === $this->getRequestType() ) {
			if ( ! is_a( $this->orderType, OrderType::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Order object can\'t be empty', 'classified-listing-pro' ) );
			}
			if ( ! is_a( $this->order, Payment::class ) ) {
				return new WP_Error( 'rtcl_authnet_error', __( 'Order object can\'t be empty', 'classified-listing-pro' ) );
			}
			if ( ! is_a( $this->payment, PaymentType::class ) ) {
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
			$data             = [ 'refId' => Functions::clean( $this->order->get_id() ) ];
			if ( 'authOnlyTransaction' === $this->transactionType || 'authCaptureTransaction' === $this->transactionType ) {
				if ( ! is_a( $this->payment->getCreditCard(), CreditCardType::class ) ) {
					return new WP_Error( 'rtcl_authnet_error', __( 'CreditCard object can\'t be empty', 'classified-listing-pro' ) );
				}
				$creditCard                  = $this->payment->getCreditCard();
				$data ['transactionRequest'] = [
					'transactionType' => Functions::clean( $this->transactionType ),
					'amount'          => Functions::format_decimal( $this->order->get_total() ),
					'payment'         => [
						'creditCard' => [
							'cardNumber'     => $creditCard->getCardNumber(),
							'expirationDate' => $creditCard->getExpirationDate(),
							'cardCode'       => $creditCard->getCardCode(),
						],
					],
					'profile'         => [
						'createProfile' => true,
					],
					'order'           => [
						'invoiceNumber' => absint( $this->orderType->getInvoiceNumber() ),
						'description'   => Functions::clean( substr( $this->orderType->getDescription(), 0, 255 ) ),
					],
					'customer'        => [
						'type'  => 'individual',
						'id'    => uniqid(),
						'email' => Functions::clean( $billing_email ?: $this->order->get_customer_email() ),
					],
					'billTo'          => [
						'firstName' => Functions::clean( substr( $first_name, 0, 50 ) ),
						'lastName'  => Functions::clean( substr( $lase_name, 0, 50 ) ),
						'address'   => Functions::clean( substr( $billing_address, 0, 30 ) ),
						'city'      => Functions::clean( substr( $billing_city, 0, 40 ) ),
						'state'     => Functions::clean( substr( $billing_state, 0, 40 ) ),
						'zip'       => Functions::clean( substr( $billing_postcode, 0, 10 ) ),
						'country'   => Functions::clean( substr( $billing_country, 0, 60 ) ),
					],
					'customerIP'      => Functions::clean( $this->order->get_customer_ip_address() ),
				];
			} else {
				$tran_meta = get_post_meta( $this->order->get_id(), '_authnet_transaction', true );

				if ( 'refundTransaction' === $this->transactionType ) {
					$data ['transactionRequest'] = [
						'transactionType' => Functions::clean( $this->transactionType ),
						'amount'          => Functions::format_decimal( $this->order->get_total() ),
						'payment'         => [
							'creditCard' => [
								'cardNumber'     => Functions::clean( $tran_meta['cc_last4'] ),
								'expirationDate' => Functions::clean( $tran_meta['cc_expiry'] ),
							],
						],
						'refTransId'      => Functions::clean( $tran_meta['transaction_id'] ),
						'order'           => [
							'invoiceNumber' => Functions::clean( $this->order->get_order_number() ),
						],
					];

				} else {
					$data ['transactionRequest'] = [
						'transactionType' => Functions::clean( $this->transactionType ),
						'amount'          => Functions::format_decimal( $this->order->get_total() ),
						'refTransId'      => Functions::clean( $tran_meta['transaction_id'] ),
						'order'           => [
							'invoiceNumber' => Functions::clean( $this->order->get_order_number() ),
						],
					];
				}
			}
		} elseif ( self::REQUEST_GET === $this->getRequestType() ) {
			$data            = [ 'refId' => $this->getRefId() ];
			$data['transId'] = $this->getTransId();
		}

		return apply_filters( 'rtcl_auth_net_payload_' . $this->getRequestType() . '_transaction', $data, $this );
	}

	/**
	 * @param PaymentType $payment
	 *
	 * @return TransactionType
	 */
	public function setPayment( PaymentType $payment ): TransactionType {
		$this->payment = $payment;

		return $this;
	}

	/**
	 * Sets a new refId
	 *
	 * @param string $refId
	 *
	 * @return self
	 */
	public function setRefId( string $refId ): TransactionType {
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
	 * @param string $requestType
	 *
	 * @return self
	 */
	public function setRequestType( string $requestType ): TransactionType {
		$this->requestType = $requestType;

		return $this;
	}

	public function getRequestType(): string {
		return $this->requestType;
	}

	/**
	 * @return string
	 */
	public function getTransId(): string {
		return $this->transId;
	}

	/**
	 * @param string $transId
	 *
	 * @return TransactionType
	 */
	public function setTransId( string $transId ): TransactionType {
		$this->transId = $transId;

		return $this;
	}


}