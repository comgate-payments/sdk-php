<?php
namespace Tests\Unit\Entity;

use Comgate\SDK\Entity\Codes\CountryCode;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\LangCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Tests\Support\UnitTester;

class PaymentCest
{
    public function getParamsTest(UnitTester $I)
    {
		$payment = (new Payment())
			->setLabel('test')
			->setEmail('test@comgate.cz')
			->setPrice(Money::ofInt(100))
			->addMethod(PaymentMethodCode::BANK_KB_BUTTON)
			->addMethod(PaymentMethodCode::BANK_FIO_BUTTON)
			->setoutMethod(PaymentMethodCode::BANK_AIRBANK_TRANSFER)
			->setCurrency(CurrencyCode::CZK)
			->setCountry(CountryCode::CZ)
			->setLang(LangCode::CS)
			->setTest(false)
			->setPreauth(false)
			->setReferenceId('123foo')
			->setInitRecurring(false)
			->setVerification(false)
			->setEmbedded(false)
			->setDynamicExpiration(true)
			->setPayerId('aaafoo');

		$paymentParams = $payment->getParams();
		$I->assertEquals([
			'test' => false,
			'prepareOnly' => true,
			'initRecurring' => false,
			'preauth' => false,
			'verification' => false,
			'embedded' => false,
			'allowedMethods' => [PaymentMethodCode::BANK_KB_BUTTON, PaymentMethodCode::BANK_FIO_BUTTON],
			'excludedMethods' => [PaymentMethodCode::BANK_AIRBANK_TRANSFER],
			'label' => 'test',
			'email' => 'test@comgate.cz',
			'price' => Money::ofInt(100),
			'curr' => 'CZK',
			'country' => 'CZ',
			'name' => '',
			'account' => '',
			'lang' => 'cs',
			'refId' => '123foo',
			'payerId' => 'aaafoo',
			'dynamicExpiration' => true,
			'method' => '',
			'phone' => '',
			'fullName' => '',
			'billingAddrCity' => '',
			'billingAddrStreet' => '',
			'billingAddrPostalCode' => '',
			'billingAddrCountry' => '',
			'delivery' => '',
			'homeDeliveryCity' => '',
			'homeDeliveryStreet' => '',
			'homeDeliveryPostalCode' => '',
			'homeDeliveryCountry' => '',
			'category' => '',
			'expirationTime' => '',
			'url_paid' => '',
			'url_cancelled' => '',
			'url_pending' => '',
			'chargeUnregulatedCardFees' => false,
			'enableApplePayGooglePay' => false,
			'initRecurringId' => '',

		], $paymentParams);
    }
}
