<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

use Comgate\SDK\Entity\Codes\CurrencyCode;

class Refund
{
	private string $transId;
	private Money $amount;
	private string $currency = CurrencyCode::CZK;
	private bool $test = false;
	private string $refId = '';

	/**
	 * @return string
	 */
	public function getTransId(): string
	{
		return $this->transId;
	}

	/**
	 * @param string $transId
	 * @return Refund
	 */
	public function setTransId(string $transId): Refund
	{
		$this->transId = $transId;
		return $this;
	}

	/**
	 * @return Money
	 */
	public function getAmount(): Money
	{
		return $this->amount;
	}

	/**
	 * @param Money $amount
	 * @return Refund
	 */
	public function setAmount(Money $amount): Refund
	{
		$this->amount = $amount;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrency(): string
	{
		return $this->currency;
	}

	/**
	 * @param string $currency
	 * @return Refund
	 */
	public function setCurrency(string $currency): Refund
	{
		$this->currency = $currency;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isTest(): bool
	{
		return $this->test;
	}

	/**
	 * @param bool $test
	 * @return Refund
	 */
	public function setTest(bool $test): Refund
	{
		$this->test = $test;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRefId(): string
	{
		return $this->refId;
	}

	/**
	 * @param string $refId
	 * @return Refund
	 */
	public function setRefId(string $refId): Refund
	{
		$this->refId = $refId;
		return $this;
	}
}
