<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\Codes\CategoryCode;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\DeliveryCode;
use Comgate\SDK\Entity\Codes\LangCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Tests\Support\UnitTester;

#[Group('payment-entity')]
class PaymentEntityCest
{
	// -----------------------------------------------------------------------
	// setRedirect / setIframe compound setters
	// -----------------------------------------------------------------------

	public function setRedirectEnablesPrepareOnlyTest(UnitTester $I): void
	{
		$payment = (new Payment())->setRedirect();

		$I->assertTrue($payment->isPrepareOnly());
		$I->assertFalse($payment->isEmbedded());
	}

	public function setIframeEnablesPrepareOnlyAndEmbeddedTest(UnitTester $I): void
	{
		$payment = (new Payment())->setIframe();

		$I->assertTrue($payment->isPrepareOnly());
		$I->assertTrue($payment->isEmbedded());
	}

	// -----------------------------------------------------------------------
	// billing address getters / setters
	// -----------------------------------------------------------------------

	public function billingAddrCityTest(UnitTester $I): void
	{
		$payment = (new Payment())->setBillingAddrCity('Prague');
		$I->assertEquals('Prague', $payment->getBillingAddrCity());
	}

	public function billingAddrStreetTest(UnitTester $I): void
	{
		$payment = (new Payment())->setBillingAddrStreet('Main Street 1');
		$I->assertEquals('Main Street 1', $payment->getBillingAddrStreet());
	}

	public function billingAddrPostalCodeTest(UnitTester $I): void
	{
		$payment = (new Payment())->setBillingAddrPostalCode('11000');
		$I->assertEquals('11000', $payment->getBillingAddrPostalCode());
	}

	public function billingAddrCountryTest(UnitTester $I): void
	{
		$payment = (new Payment())->setBillingAddrCountry('CZ');
		$I->assertEquals('CZ', $payment->getBillingAddrCountry());
	}

	// -----------------------------------------------------------------------
	// home delivery getters / setters
	// -----------------------------------------------------------------------

	public function homeDeliveryCityTest(UnitTester $I): void
	{
		$payment = (new Payment())->setHomeDeliveryCity('Brno');
		$I->assertEquals('Brno', $payment->getHomeDeliveryCity());
	}

	public function homeDeliveryStreetTest(UnitTester $I): void
	{
		$payment = (new Payment())->setHomeDeliveryStreet('Freedom Square 5');
		$I->assertEquals('Freedom Square 5', $payment->getHomeDeliveryStreet());
	}

	public function homeDeliveryPostalCodeTest(UnitTester $I): void
	{
		$payment = (new Payment())->setHomeDeliveryPostalCode('60200');
		$I->assertEquals('60200', $payment->getHomeDeliveryPostalCode());
	}

	public function homeDeliveryCountryTest(UnitTester $I): void
	{
		$payment = (new Payment())->setHomeDeliveryCountry('SK');
		$I->assertEquals('SK', $payment->getHomeDeliveryCountry());
	}

	public function deliveryTest(UnitTester $I): void
	{
		$payment = (new Payment())->setDelivery(DeliveryCode::HOME_DELIVERY);
		$I->assertEquals(DeliveryCode::HOME_DELIVERY, $payment->getDelivery());
	}

	// -----------------------------------------------------------------------
	// optional fields
	// -----------------------------------------------------------------------

	public function phoneTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPhone('+420123456789');
		$I->assertEquals('+420123456789', $payment->getPhone());
	}

	public function fullNameTest(UnitTester $I): void
	{
		$payment = (new Payment())->setFullName('Jan Novák');
		$I->assertEquals('Jan Novák', $payment->getFullName());
	}

	public function categoryTest(UnitTester $I): void
	{
		$payment = (new Payment())->setCategory(CategoryCode::PHYSICAL_GOODS_ONLY);
		$I->assertEquals(CategoryCode::PHYSICAL_GOODS_ONLY, $payment->getCategory());
	}

	public function expirationTimeTest(UnitTester $I): void
	{
		$payment = (new Payment())->setExpirationTime('2099-12-31T23:59:59');
		$I->assertEquals('2099-12-31T23:59:59', $payment->getExpirationTime());
	}

	public function expirationTimeNullableTest(UnitTester $I): void
	{
		// expirationTime defaults to '' (empty string)
		$payment = new Payment();
		$I->assertEquals('', $payment->getExpirationTime());
	}

	public function transactionIdTest(UnitTester $I): void
	{
		$payment = (new Payment())->setTransactionId('TX-001');
		$I->assertEquals('TX-001', $payment->getTransactionId());
	}

	public function payerIdTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPayerId('PAYER-42');
		$I->assertEquals('PAYER-42', $payment->getPayerId());
	}

	public function applePayPayloadTest(UnitTester $I): void
	{
		$payment = (new Payment())->setApplePayPayload('base64payloaddata');
		$I->assertEquals('base64payloaddata', $payment->getApplePayPayload());
	}

	public function initRecurringIdTest(UnitTester $I): void
	{
		$payment = (new Payment())->setInitRecurringId('INIT-TX-001');
		$I->assertEquals('INIT-TX-001', $payment->getInitRecurringId());
	}

	public function dynamicExpirationTest(UnitTester $I): void
	{
		$payment = (new Payment())->setDynamicExpiration(true);
		$I->assertTrue($payment->isDynamicExpiration());
	}

	// -----------------------------------------------------------------------
	// URL setters — both families
	// -----------------------------------------------------------------------

	public function urlPaidRedirectTest(UnitTester $I): void
	{
		$payment = (new Payment())->setUrlPaidRedirect('https://example.com/ok');
		$I->assertEquals('https://example.com/ok', $payment->getUrlPaidRedirect());
	}

	public function urlCancelledRedirectTest(UnitTester $I): void
	{
		$payment = (new Payment())->setUrlCancelledRedirect('https://example.com/cancel');
		$I->assertEquals('https://example.com/cancel', $payment->getUrlCancelledRedirect());
	}

	public function urlPendingRedirectTest(UnitTester $I): void
	{
		$payment = (new Payment())->setUrlPendingRedirect('https://example.com/pending');
		$I->assertEquals('https://example.com/pending', $payment->getUrlPendingRedirect());
	}

	// -----------------------------------------------------------------------
	// getParams / setParams
	// -----------------------------------------------------------------------

	public function getParamsReturnsArrayTest(UnitTester $I): void
	{
		$payment = new Payment();
		$I->assertIsArray($payment->getParams());
	}

	public function setParamsOverridesAllParamsTest(UnitTester $I): void
	{
		$payment = new Payment();
		$payment->setParams(['custom_key' => 'custom_value']);

		$I->assertEquals(['custom_key' => 'custom_value'], $payment->getParams());
	}
}
