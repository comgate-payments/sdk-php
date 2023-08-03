<?php

namespace Comgate\SDK\Entity\Request;

class SimulationRequest implements IRequest
{
        /**
         * 
         * @var array<string, int|string>
         */
	protected array $params = [];

        /**
         * 
         * @param array<string, int|string> $params
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
         * 
         * @return array<string, int|string>
         */
	public function toArray(): array
	{
		return $this->getParams();
	}

	/**
	 * @return array<string, int|string>
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @param array<string, int|string> $params
	 * @return SimulationRequest
	 */
	public function setParams(array $params): SimulationRequest
	{
		$this->params = $params;
		return $this;
	}
}
