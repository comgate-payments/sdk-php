<?php

namespace Comgate\SDK\Entity\Request;

class SimulationRequest implements IRequest
{
	protected array $params = [];

	public function __construct(array $params)
	{
		$this->setParams($params);
	}

	public function getUrn(): string
	{
		return 'simulation';
	}

	public function toArray(): array
	{
		return $this->getParams();
	}

	/**
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @param array $params
	 * @return SimulationRequest
	 */
	public function setParams(array $params): SimulationRequest
	{
		$this->params = $params;
		return $this;
	}
}
