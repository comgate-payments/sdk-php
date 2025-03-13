<?php

namespace Comgate\SDK\Entity;

use Exception;

class PaymentCard
{
	private ?string $cardNumber;
	private ?string $cardExpiration;
	private ?string $cardCvv;

	/**
	 * @param string|null $cardNumber 16 digit card number
	 * @param string|null $cardExpiration YYYYMM format
	 * @param string|null $cardCvv 3 digit
	 */
	public function __construct(?string $cardNumber = null, ?string $cardExpiration = null, ?string $cardCvv = null)
	{
		$this->cardNumber = $cardNumber;
		$this->cardExpiration = $cardExpiration;
		$this->cardCvv = $cardCvv;

		$this->validate();
	}

	/**
	 * @return string|null
	 */
	public function getCardNumber(): ?string
	{
		return $this->cardNumber;
	}

	/**
	 * @param string $cardNumber
	 * @return PaymentCard
	 */
	public function setCardNumber(string $cardNumber): PaymentCard
	{
		$this->cardNumber = $cardNumber;
		$this->validate();
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCardExpiration(): ?string
	{
		return $this->cardExpiration;
	}

	/**
	 * @param string $cardExpiration
	 * @return PaymentCard
	 */
	public function setCardExpiration(string $cardExpiration): PaymentCard
	{
		$this->cardExpiration = $cardExpiration;
		$this->validate();
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCardCvv(): ?string
	{
		return $this->cardCvv;
	}

	/**
	 * @param string $cardCvv
	 * @return PaymentCard
	 */
	public function setCardCvv(string $cardCvv): PaymentCard
	{
		$this->cardCvv = $cardCvv;
		$this->validate();
		return $this;
	}

	/**
	 * @return true
	 * @throws Exception
	 */
	private function validate() {
		if (!empty($this->cardNumber) && !preg_match('/^[0-9]{16}$/', $this->cardNumber)) {
			throw new Exception(sprintf('Invalid card number: %s. (Required 16 digits)', $this->cardNumber));
		}
		if (!empty($this->cardExpiration) && !preg_match('/^[0-9]{6}$/', $this->cardExpiration)) {
			throw new Exception(sprintf('Invalid card expiration: %s. (Required format YYYYMM)', $this->cardExpiration));
		}
		if (!empty($this->cardCvv) && !preg_match('/^[0-9]{3}$/', $this->cardCvv)) {
			throw new Exception(sprintf('Invalid card CVV: %s. (Required 3 digits)', $this->cardCvv));
		}
		return true;
	}

}
