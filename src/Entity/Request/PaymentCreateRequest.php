<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\LogicalException;

class PaymentCreateRequest implements IRequest
{

	/** @var Payment */
	private $payment;

	public function __construct(Payment $payment)
	{
		$this->payment = $payment;
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'create';
	}

	/**
	 * @return array<string, bool|string|int|null>
	 */
	public function toArray(): array
	{
		// Required
		$output = $this->payment->getParams();

		$output['price'] = $this->payment->getPrice()->get(); // in cents 10.25 => 1025
        $output['chargeUnregulatedCardFees'] = $this->payment->getChargeUnregulatedCardFees();
        $output['enableApplePayGooglePay'] = $this->payment->getEnableApplePayGooglePay();
		$output['prepareOnly'] = $this->payment->isPrepareOnly() ? 'true' : 'false';
		$output['method'] = implode('+', $this->payment->getAllowedMethods());
		unset($output['allowedMethods']);

		if (count($this->payment->getExcludedMethods()) > 0) {
			$output['method'] = ltrim($output['method'] . '-' . implode('-', $this->payment->getExcludedMethods()), '-');
		}
		unset($output['excludedMethods']);

		// Optional
		$output['preauth'] = $this->payment->isPreauth() ? 'true' : 'false';
		$output['test'] = $this->payment->isTest() ? 'true' : 'false';
		$output['verification'] = $this->payment->isVerification() ? 'true' : 'false';
		$output['embedded'] = $this->payment->isEmbedded() ? 'true' : 'false';
		$output['initRecurring'] = $this->payment->isInitRecurring() ? 'true' : 'false';
		$output['dynamicExpiration'] = $this->payment->isDynamicExpiration() ? 'true' : 'false';

		return $output;
	}

}




