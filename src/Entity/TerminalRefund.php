<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

class TerminalRefund
{
	/** @var Money */
	private $price;

	/** @var string */
	private $curr;

	/** @var string|null */
	private $refId;

	/**
	 * @return Money
	 */
	public function getPrice(): Money
	{
		return $this->price;
	}

	/**
	 * @param int|float|Money $price
	 * @return self
	 */
	public function setPrice($price): self
	{
		$this->price = Money::of($price);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurr(): string
	{
		return $this->curr;
	}

	/**
	 * @param string $curr Kód měny dle ISO 4217 (CZK, EUR)
	 * @return self
	 */
	public function setCurr(string $curr): self
	{
		$this->curr = $curr;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getRefId(): ?string
	{
		return $this->refId;
	}

	/**
	 * @param string|null $refId Variabilní symbol nebo číslo objednávky na straně klienta
	 * @return self
	 */
	public function setRefId(?string $refId): self
	{
		$this->refId = $refId;
		return $this;
	}
}
