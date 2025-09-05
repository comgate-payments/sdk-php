<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;

class PaymentCreateRequest implements IRequest
{

	/** @var Payment */
	protected $payment;

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
		$output['curr'] = $this->payment->getCurrency();
		$output['label'] = $this->payment->getLabel();
		$output['refId'] = $this->payment->getReferenceId();
		$output['email'] = $this->payment->getEmail();
		$output['phone'] = $this->payment->getPhone();
		$output['fullName'] = $this->payment->getFullName();
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
		$output['initRecurring'] = $this->payment->isInitRecurring() ? 'true' : 'false';
		$output['dynamicExpiration'] = $this->payment->isDynamicExpiration() ? 'true' : 'false';
		$output['account'] = $this->payment->getAccount() ?? '';
		$output['billingAddrCity'] = $this->payment->getBillingAddrCity() ?? '';
		$output['billingAddrStreet'] = $this->payment->getBillingAddrStreet() ?? '';
		$output['billingAddrPostalCode'] = $this->payment->getBillingAddrPostalCode() ?? '';
		$output['billingAddrCountry'] = $this->payment->getBillingAddrCountry() ?? '';
		$output['delivery'] = $this->payment->getDelivery() ?? '';
		$output['homeDeliveryCity'] = $this->payment->getHomeDeliveryCity() ?? '';
		$output['homeDeliveryStreet'] = $this->payment->getHomeDeliveryStreet() ?? '';
		$output['homeDeliveryPostalCode'] = $this->payment->getHomeDeliveryPostalCode() ?? '';
		$output['homeDeliveryCountry'] = $this->payment->getHomeDeliveryCountry() ?? '';
		$output['category'] = $this->payment->getCategory() ?? '';
		$output['name'] = $this->payment->getName() ?? '';
		$output['lang'] = $this->payment->getLang() ?? '';
		if ($this->payment->getExpirationTime() !== null) {
			$output['expirationTime'] = $this->payment->getExpirationTime();
		}
		$output['url_paid'] = $this->payment->getUrlPaidRedirect() ?? '';
		$output['url_cancelled'] = $this->payment->getUrlCancelledRedirect() ?? '';
		$output['url_pending'] = $this->payment->getUrlPendingRedirect() ?? '';
		$output['chargeUnregulatedCardFees'] = $this->payment->getChargeUnregulatedCardFees() ? 'true' : 'false';
		$output['enableApplePayGooglePay'] = $this->payment->getEnableApplePayGooglePay() ? 'true' : 'false';
		$output['embedded'] = $this->payment->isEmbedded() ? 'true' : 'false';

		return $output;
	}

}




