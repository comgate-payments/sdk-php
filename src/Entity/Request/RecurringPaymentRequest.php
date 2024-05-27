<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\LogicalException;

class RecurringPaymentRequest implements IRequest
{
	private Payment $payment;

	public function __construct(Payment $payment){
		$this->setPayment($payment);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'recurring';
	}

	/**
	 * @return array<string, string|int>
	 */
	public function toArray(): array
	{
		// Required
		$output = [
			'country' => $this->getPayment()->getCountry(),
			'test' => $this->getPayment()->isTest(),
			'price' => $this->getPayment()->getPrice()->get(),
			'curr' => $this->getPayment()->getCurrency(),
			'label' => $this->getPayment()->getLabel(),
			'refId' => $this->getPayment()->getReferenceId(),
			'account' => $this->getPayment()->getAccount(),
			'email' => $this->getPayment()->getEmail(),
			'name' => $this->getPayment()->getName(),
			'prepareOnly' => $this->getPayment()->isPrepareOnly(),
			'initRecurringId' => $this->getPayment()->getInitRecurringId(),
		];

		return $output;
	}

	/**
	 * @return Payment
	 */
	public function getPayment(): Payment
	{
		return $this->payment;
	}

	/**
	 * @param Payment $payment
	 * @return RecurringPaymentRequest
	 */
	public function setPayment(Payment $payment): RecurringPaymentRequest
	{
		$this->payment = $payment;
		return $this;
	}
}
