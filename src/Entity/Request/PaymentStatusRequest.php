<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;

class PaymentStatusRequest implements IRequest
{

	/** @var string */
	private $transId;

	public function __construct(string $transId)
	{
		$this->setTransId($transId);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'status';
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return [
			'transId' => $this->getTransId(),
		];
	}

	/**
	 * @return string
	 */
	public function getTransId(): string
	{
		return $this->transId;
	}

	/**
	 * @param string $transId
	 * @return PaymentStatusRequest
	 */
	public function setTransId(string $transId): PaymentStatusRequest
	{
		$this->transId = $transId;
		return $this;
	}
}
