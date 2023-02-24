<?php

namespace RtclPro\Gateways\Authorize\lib\Types;

class CreditCardSimpleType {

	/**
	 * @property string $cardNumber
	 */
	private $cardNumber = null;

	/**
	 * @property string $expirationDate
	 */
	private $expirationDate = null;

	/**
	 * Gets as cardNumber
	 *
	 * @return string
	 */
	public function getCardNumber() {
		return $this->cardNumber;
	}

	/**
	 * Sets a new cardNumber
	 *
	 * @param string $cardNumber
	 *
	 * @return self
	 */
	public function setCardNumber( $cardNumber ) {
		$this->cardNumber = $cardNumber;

		return $this;
	}

	/**
	 * Gets as expirationDate
	 *
	 * @return string
	 */
	public function getExpirationDate() {
		return $this->expirationDate;
	}

	/**
	 * Sets a new expirationDate
	 *
	 * @param string $expirationDate
	 *
	 * @return self
	 */
	public function setExpirationDate( $expirationDate ) {
		$this->expirationDate = $expirationDate;

		return $this;
	}

}