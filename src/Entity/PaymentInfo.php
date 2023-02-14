<?php

namespace Comgate\SDK\Entity;

class PaymentInfo
{
	protected array $data = [];

	public function fromArray(array $data){
		$this->setData($data);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @param array $data
	 * @return PaymentInfo
	 */
	public function setData(array $data): PaymentInfo
	{
		$this->data = $data;
		return $this;
	}
}
