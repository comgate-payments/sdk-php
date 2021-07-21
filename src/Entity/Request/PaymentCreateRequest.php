<?php declare(strict_types = 1);
namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;

class PaymentCreateRequest
{
	private Payment $payment;

	public function __construct(Payment $payment){

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
			'prepareOnly' => $this->payment->getPrepareOnly() ? 'true' : 'false',
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
			$output['preauth'] = $this->payment->getPreauth() ? 'true' : 'false';
		}

		if ($this->payment->isInitRecurring() !== null) {
			if ($this->payment->getPrepareOnly() !== true) {
				throw new LogicalException('Field initRecurring requires prepareOnly=true');
			}

			$output['initRecurring'] = $this->payment->getInitRecurring() ? 'true' : 'false';
		}

		if ($this->payment->isVerification() !== null) {
			$output['initRecurring'] = $this->payment->getVerification() ? 'true' : 'false';
		}

		if ($this->payment->isEmbedded() !== null) {
			$output['embedded'] = $this->payment->getEmbedded() ? 'true' : 'false';
		}

		if ($this->payment->isEetReport() !== null) {
			$output['eetReport'] = $this->payment->getEetReport() ? 'true' : 'false';
		}

		if ($this->payment->getEetData() !== []) {
			$output['eetData'] = $this->payment->getEetData();
		}

		return $output;
	}
}
