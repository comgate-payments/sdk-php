<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;

class PaymentCancelRequest implements IRequest
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
		return 'cancel';
	}

	/**
	 * @return mixed[]
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
	 * @return PaymentCancelRequest
	 */
	public function setTransId(string $transId): PaymentCancelRequest
	{
		$this->transId = $transId;
		return $this;
	}
}
