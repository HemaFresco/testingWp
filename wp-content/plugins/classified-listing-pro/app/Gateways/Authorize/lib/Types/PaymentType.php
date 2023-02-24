<?php

namespace RtclPro\Gateways\Authorize\lib\Types;

class PaymentType {
	/**
	 * @property CreditCardType $creditCard
	 */
	private CreditCardType $creditCard;

//	/**
//	 * //     * @property BankAccountType $bankAccount
//	 */
//	private $bankAccount = null;
//
//	/**
//	 * @property \net\authorize\api\contract\v1\CreditCardTrackType $trackData
//	 */
//	private $trackData = null;
//
//	/**
//	 * @property \net\authorize\api\contract\v1\EncryptedTrackDataType
//	 * $encryptedTrackData
//	 */
//	private $encryptedTrackData = null;
//
//	/**
//	 * @property \net\authorize\api\contract\v1\PayPalType $payPal
//	 */
//	private $payPal = null;
//
//	/**
//	 * @property \net\authorize\api\contract\v1\OpaqueDataType $opaqueData
//	 */
//	private $opaqueData = null;
//
//	/**
//	 * @property \net\authorize\api\contract\v1\PaymentEmvType $emv
//	 */
//	private $emv = null;
//
//	/**
//	 * @property string $dataSource
//	 */
//	private $dataSource = null;

	/**
	 * Gets as creditCard
	 *
	 * @return CreditCardType
	 */
	public function getCreditCard(): CreditCardType {
		return $this->creditCard;
	}

	/**
	 * Sets a new creditCard
	 *
	 * @param CreditCardType $creditCard
	 *
	 * @return self
	 */
	public function setCreditCard( CreditCardType $creditCard ) {
		$this->creditCard = $creditCard;

		return $this;
	}
//
//	/**
//	 * Gets as bankAccount
//	 *
//	 * @return \net\authorize\api\contract\v1\BankAccountType
//	 */
//	public function getBankAccount() {
//		return $this->bankAccount;
//	}
//
//	/**
//	 * Sets a new bankAccount
//	 *
//	 * @param \net\authorize\api\contract\v1\BankAccountType $bankAccount
//	 *
//	 * @return self
//	 */
//	public function setBankAccount( \net\authorize\api\contract\v1\BankAccountType $bankAccount ) {
//		$this->bankAccount = $bankAccount;
//
//		return $this;
//	}
//
//	/**
//	 * Gets as trackData
//	 *
//	 * @return \net\authorize\api\contract\v1\CreditCardTrackType
//	 */
//	public function getTrackData() {
//		return $this->trackData;
//	}
//
//	/**
//	 * Sets a new trackData
//	 *
//	 * @param \net\authorize\api\contract\v1\CreditCardTrackType $trackData
//	 *
//	 * @return self
//	 */
//	public function setTrackData( \net\authorize\api\contract\v1\CreditCardTrackType $trackData ) {
//		$this->trackData = $trackData;
//
//		return $this;
//	}
//
//	/**
//	 * Gets as encryptedTrackData
//	 *
//	 * @return \net\authorize\api\contract\v1\EncryptedTrackDataType
//	 */
//	public function getEncryptedTrackData() {
//		return $this->encryptedTrackData;
//	}
//
//	/**
//	 * Sets a new encryptedTrackData
//	 *
//	 * @param \net\authorize\api\contract\v1\EncryptedTrackDataType $encryptedTrackData
//	 *
//	 * @return self
//	 */
//	public function setEncryptedTrackData( \net\authorize\api\contract\v1\EncryptedTrackDataType $encryptedTrackData ) {
//		$this->encryptedTrackData = $encryptedTrackData;
//
//		return $this;
//	}
//
//	/**
//	 * Gets as payPal
//	 *
//	 * @return \net\authorize\api\contract\v1\PayPalType
//	 */
//	public function getPayPal() {
//		return $this->payPal;
//	}
//
//	/**
//	 * Sets a new payPal
//	 *
//	 * @param \net\authorize\api\contract\v1\PayPalType $payPal
//	 *
//	 * @return self
//	 */
//	public function setPayPal( \net\authorize\api\contract\v1\PayPalType $payPal ) {
//		$this->payPal = $payPal;
//
//		return $this;
//	}

}