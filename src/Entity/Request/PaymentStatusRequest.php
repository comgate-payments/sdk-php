<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;

class PaymentStatusRequest
{

	/** @var Payment */
	private $payment;

	private function __construct(Payment $payment)
	{
		$this->payment = $payment;
	}

	/**
	 * @return static
	 */
	public static function of(Payment $payment): self
	{
		return new static($payment);
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'transId' => $this->payment->getTransactionId(),
		];
	}

}
