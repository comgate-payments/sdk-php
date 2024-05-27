<?php

namespace Comgate\SDK\Entity;

class PaymentInfo
{
	/**
	 * @var array<string, string|int>
	 */
	protected array $data = [];

	/**
	 * @param array<string, string|int> $data
	 * @return $this
	 */
	public function fromArray(array $data){
		$this->setData($data);
		return $this;
	}

	/**
	 * @return array<string, string|int>
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @param array<string, string|int> $data
	 * @return PaymentInfo
	 */
	public function setData(array $data): PaymentInfo
	{
		$this->data = $data;
		return $this;
	}
}
