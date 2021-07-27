<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\LogicalException;

class PaymentCreateRequest
{

	private Payment $payment;

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
		// Required

		$output = [
			'price' => $this->payment->getPrice()->get(), // in cents 10.25 => 1025
			'curr' => $this->payment->getCurrency(),
			'label' => $this->payment->getLabel(),
			'refId' => $this->payment->getReferenceId(),
			'email' => $this->payment->getEmail(),
			'prepareOnly' => $this->payment->isPrepareOnly() ? 'true' : 'false',
			'method' => null,
		];

		if ($this->payment->getAllowedMethods() === [] && $this->payment->getExcludedMethods() !== []) {
			throw new LogicalException('There must be at least one allowed method');
		}

		if ($this->payment->getAllowedMethods() !== []) {
			$output['method'] = implode('+', $this->payment->getAllowedMethods());
		}

		if ($this->payment->getExcludedMethods() !== []) {
			$output['method'] = ltrim($output['method'] . '-' . implode('-', $this->payment->getExcludedMethods()), '-');
		}

		// Optional

		if ($this->payment->getPhone() !== null) {
			$output['phone'] = $this->payment->getPhone();
		}

		if ($this->payment->getName() !== null) {
			$output['name'] = $this->payment->getName();
		}

		if ($this->payment->getCountry() !== null) {
			$output['country'] = $this->payment->getCountry();
		}

		if ($this->payment->getAccount() !== null) {
			$output['account'] = $this->payment->getAccount();
		}

		if ($this->payment->getLang() !== null) {
			$output['lang'] = $this->payment->getLang();
		}

		if ($this->payment->isPreauth() !== null) {
			$output['preauth'] = $this->payment->isPreauth() ? 'true' : 'false';
		}

		if ($this->payment->isInitRecurring() !== null) {
			if ($this->payment->isPrepareOnly() !== true) {
				throw new LogicalException('Field initRecurring requires prepareOnly=true');
			}

			$output['initRecurring'] = $this->payment->isInitRecurring() ? 'true' : 'false';
		}

		if ($this->payment->isVerification() !== null) {
			$output['initRecurring'] = $this->payment->isVerification() ? 'true' : 'false';
		}

		if ($this->payment->isEmbedded() !== null) {
			$output['embedded'] = $this->payment->isEmbedded() ? 'true' : 'false';
		}

		if ($this->payment->isEetReport() !== null) {
			$output['eetReport'] = $this->payment->isEetReport() ? 'true' : 'false';
		}

		if ($this->payment->getEetData() !== []) {
			$output['eetData'] = $this->payment->getEetData();
		}

		return $output;
	}

}
