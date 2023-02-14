<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\Refund;

class PaymentRefundRequest implements IRequest
{

	/** @var $refund */
	private Refund $refund;

	public function __construct(Refund $refund)
	{
		$this->setRefund($refund);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'refund';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'transId' => $this->getRefund()->getTransId(),
			'amount' => $this->getRefund()->getAmount()->get(),
			'curr' => $this->getRefund()->getCurrency(),
			'test' => $this->getRefund()->isTest() ? 'true' : 'false',
			'refId' => $this->getRefund()->getRefId(),
		];
	}

	/**
	 * @return Refund
	 */
	public function getRefund(): Refund
	{
		return $this->refund;
	}

	/**
	 * @param Refund $refund
	 * @return PaymentRefundRequest
	 */
	public function setRefund(Refund $refund): self
	{
		$this->refund = $refund;
		return $this;
	}

}
