<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use Codeception\Module;
use Comgate\SDK\Entity\Codes\CountryCode;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\LangCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class PaymentGenerator extends Module
{
	/**
	 * @return Payment
	 */
	public function createPayment(){
		$payment = new Payment();
		$payment->setLabel('SDK test payment')
			->setEmail('sdk-test@comgate.cz')
			->setPrice(Money::ofInt(100))
			->setCurrency('CZK')
			->setCountry('CZ')
			->setLang('CS')
			->setTest(false)
			->setMethods(['ALL'])
			->setReferenceId('order1234');

		return $payment;
	}
}
