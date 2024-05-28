<?php

namespace Comgate\SDK\Entity\Request;

/**
 * The class allows switching the state of a test payment on the payment gateway.
 */
class SimulationRequest implements IRequest
{
	/**
	 * @var array<string, string> $params
	 */
	protected array $params = [];

	/**
	 * @param array<string, string> $params
	 */
	public function __construct(array $params)
	{
		$this->setParams($params);
	}

	public function getUrn(): string
	{
		return 'simulation';
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return $this->getParams();
	}

	/**
	 * @return array<string, string>
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @param array<string, string> $params
	 * @return SimulationRequest
	 */
	public function setParams(array $params): SimulationRequest
	{
		$this->params = $params;
		return $this;
	}
}
