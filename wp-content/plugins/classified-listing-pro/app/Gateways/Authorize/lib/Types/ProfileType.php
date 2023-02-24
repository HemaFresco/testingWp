<?php

namespace RtclPro\Gateways\Authorize\lib\Types;

class ProfileType {

	/**
	 * @var string|null
	 */
	private ?string $customerProfileId = null;
	/**
	 * @var string|null
	 */
	private ?string $customerPaymentProfileId = null;
	/**
	 * @var string|null
	 */
	private ?string $customerAddressId = null;

	/**
	 * @return string|null
	 */
	public function getCustomerProfileId(): ?string {
		return $this->customerProfileId;
	}

	/**
	 * @param string|null $customerProfileId
	 *
	 * @return ProfileType
	 */
	public function setCustomerProfileId( ?string $customerProfileId ): ProfileType {
		$this->customerProfileId = $customerProfileId;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCustomerPaymentProfileId(): ?string {
		return $this->customerPaymentProfileId;
	}

	/**
	 * @param string|null $customerPaymentProfileId
	 *
	 * @return ProfileType
	 */
	public function setCustomerPaymentProfileId( ?string $customerPaymentProfileId ): ProfileType {
		$this->customerPaymentProfileId = $customerPaymentProfileId;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCustomerAddressId(): ?string {
		return $this->customerAddressId;
	}

	/**
	 * @param string|null $customerAddressId
	 *
	 * @return ProfileType
	 */
	public function setCustomerAddressId( ?string $customerAddressId ): ProfileType {
		$this->customerAddressId = $customerAddressId;

		return $this;
	}
}