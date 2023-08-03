<?php

namespace Comgate\SDK\Entity;

class PaymentInfo
{
        /**
         * 
         * @var array<string, int|string>
         */
	protected array $data = [];

        /**
         * 
         * @param array<string, int|string> $data
         * @return self
         */
	public function fromArray(array $data): self
        {
		$this->setData($data);
		return $this;
	}

	/**
	 * @return array<string, int|string>
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @param array<string, int|string> $data
	 * @return PaymentInfo
	 */
	public function setData(array $data): PaymentInfo
	{
		$this->data = $data;
		return $this;
	}
}
