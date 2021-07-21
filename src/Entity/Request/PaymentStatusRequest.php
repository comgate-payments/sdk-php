<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;

class PaymentStatusRequest
{
	private Payment $payment;

	public function __construct(Payment $payment){

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
