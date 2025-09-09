<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class AppleDomainAssociationRequest implements IRequest
{
	protected string $method;
	protected string $currency;

	public function __construct(string $method, string $currency)
	{
		$this->setMethod($method);
		$this->setCurrency($currency);
	}

	public function getUrn(): string
	{
		return 'appleDomainAssociation';
	}

	public function toArray(): array
	{
		return[
			'method' => $this->getMethod(),
			'currency' => $this->getCurrency(),
		];
	}

	/**
	 * @return string
	 */
	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 * @return $this
	 */
	public function setMethod(string $method): self
	{
		$this->method = $method;
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
	 * @return $this
	 */
	public function setCurrency(string $currency): self
	{
		$this->currency = $currency;
		return $this;
	}
}
