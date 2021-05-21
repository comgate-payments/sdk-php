<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

class PaymentStatus extends Entity
{

	protected string $transactionId;

	final private function __construct()
	{
	}

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new static();
	}

	public function getTransactionId(): string
	{
		return $this->transactionId;
	}

	public function withTransactionId(string $transactionId): self
	{
		$this->transactionId = $transactionId;

		return $this;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'transId' => $this->transactionId,
		];
	}

}
