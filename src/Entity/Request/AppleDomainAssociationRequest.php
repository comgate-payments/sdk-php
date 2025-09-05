<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class AppleDomainAssociationRequest implements IRequest
{
	protected string $method;

	public function __construct(string $method)
	{
		$this->setMethod($method);
	}

	public function getUrn(): string
	{
		return 'appleDomainAssociation';
	}

	public function toArray(): array
	{
		return[
			'method' => $this->getMethod(),
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
}
