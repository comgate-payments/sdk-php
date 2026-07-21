<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Request;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\Request\PaymentCreateRequest;
use Tests\Support\UnitTester;

#[Group('payment-create-request')]
class PaymentCreateRequestCest
{
	public function urnIsPaymentJsonTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPrice(Money::ofInt(1));
		$I->assertEquals('payment.json', (new PaymentCreateRequest($payment))->getUrn());
	}

	// -----------------------------------------------------------------------
	// method encoding
	// -----------------------------------------------------------------------

	public function noMethodsProducesEmptyMethodFieldTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPrice(Money::ofInt(10));
		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('', $result['method']);
		$I->assertArrayNotHasKey('allowedMethods', $result);
		$I->assertArrayNotHasKey('excludedMethods', $result);
	}

	public function singleAllowedMethodJoinedWithPlusTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(10))
			->setMethods([PaymentMethodCode::ALL_BANKS]);

		$I->assertEquals('BANK_ALL', (new PaymentCreateRequest($payment))->toArray()['method']);
	}

	public function multipleAllowedMethodsJoinedWithPlusTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(10))
			->setMethods([PaymentMethodCode::ALL_BANKS, PaymentMethodCode::BANK_RB_BUTTON])
			->addMethod(PaymentMethodCode::CARD_CARD_CZ_CSOB_2);

		$I->assertEquals(
			PaymentMethodCode::ALL_BANKS . '+' . PaymentMethodCode::BANK_RB_BUTTON . '+' . PaymentMethodCode::CARD_CARD_CZ_CSOB_2,
			(new PaymentCreateRequest($payment))->toArray()['method']
		);
	}

	public function allowedPlusExcludedCombinedTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(10))
			->setMethods([PaymentMethodCode::ALL_BANKS, PaymentMethodCode::BANK_RB_BUTTON])
			->setoutMethod(PaymentMethodCode::BANK_FIO_BUTTON);

		$expected = PaymentMethodCode::ALL_BANKS . '+' . PaymentMethodCode::BANK_RB_BUTTON
			. '-' . PaymentMethodCode::BANK_FIO_BUTTON;

		$I->assertEquals($expected, (new PaymentCreateRequest($payment))->toArray()['method']);
	}

	/**
	 * Regression test for the ltrim bug:
	 * When there are NO allowed methods and at least one excluded method,
	 * ltrim strips the leading '-', making the excluded method appear
	 * as an allowed method instead.
	 *
	 * Current (buggy) behaviour is asserted here so that a future fix
	 * will produce an intentional test failure rather than a silent regression.
	 */
	public function onlyExcludedMethodsLtrimRemovesLeadingDashBugTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(10))
			->setoutMethod(PaymentMethodCode::BANK_FIO_BUTTON);

		$result = (new PaymentCreateRequest($payment))->toArray()['method'];

		// Bug: leading '-' is stripped, method treated as allowed instead of excluded
		$I->assertEquals(PaymentMethodCode::BANK_FIO_BUTTON, $result);
		$I->assertStringStartsNotWith('-', $result);
	}

	// -----------------------------------------------------------------------
	// price conversion
	// -----------------------------------------------------------------------

	public function priceConvertedToCentsTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPrice(Money::ofInt(25));
		$I->assertEquals(2500, (new PaymentCreateRequest($payment))->toArray()['price']);
	}

	// -----------------------------------------------------------------------
	// URL fields
	// -----------------------------------------------------------------------

	public function urlFieldsDefaultToEmptyStringTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPrice(Money::ofInt(1));
		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('', $result['url_paid']);
		$I->assertEquals('', $result['url_cancelled']);
		$I->assertEquals('', $result['url_pending']);
	}

	public function urlFieldsFromRedirectSettersTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(1))
			->setUrlPaidRedirect('https://example.com/paid')
			->setUrlCancelledRedirect('https://example.com/cancel')
			->setUrlPendingRedirect('https://example.com/pending');

		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('https://example.com/paid', $result['url_paid']);
		$I->assertEquals('https://example.com/cancel', $result['url_cancelled']);
		$I->assertEquals('https://example.com/pending', $result['url_pending']);
	}

	// -----------------------------------------------------------------------
	// nullable flags
	// -----------------------------------------------------------------------

	public function chargeUnregulatedCardFeesAbsentWhenNullTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPrice(Money::ofInt(1));
		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertArrayNotHasKey('chargeUnregulatedCardFees', $result);
		$I->assertArrayNotHasKey('enableApplePayGooglePay', $result);
	}

	public function chargeUnregulatedCardFeesPresentAsFalseStringTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(1))
			->setChargeUnregulatedCardFees(false)
			->setEnableApplePayGooglePay(false);

		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('false', $result['chargeUnregulatedCardFees']);
		$I->assertEquals('false', $result['enableApplePayGooglePay']);
	}

	public function chargeUnregulatedCardFeesPresentAsTrueStringTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(1))
			->setChargeUnregulatedCardFees(true)
			->setEnableApplePayGooglePay(true);

		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('true', $result['chargeUnregulatedCardFees']);
		$I->assertEquals('true', $result['enableApplePayGooglePay']);
	}

	// -----------------------------------------------------------------------
	// boolean flags as strings
	// -----------------------------------------------------------------------

	public function boolFlagsConvertedToStringsTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(1))
			->setTest(true)
			->setPreauth(true)
			->setInitRecurring(true)
			->setEmbedded(true)
			->setVerification(true);

		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('true', $result['test']);
		$I->assertEquals('true', $result['preauth']);
		$I->assertEquals('true', $result['initRecurring']);
		$I->assertEquals('true', $result['embedded']);
		$I->assertEquals('true', $result['verification']);
	}

	// -----------------------------------------------------------------------
	// billing / delivery fields
	// -----------------------------------------------------------------------

	public function billingAndDeliveryFieldsDefaultToEmptyStringTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPrice(Money::ofInt(1));
		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('', $result['billingAddrCity']);
		$I->assertEquals('', $result['billingAddrStreet']);
		$I->assertEquals('', $result['billingAddrPostalCode']);
		$I->assertEquals('', $result['billingAddrCountry']);
		$I->assertEquals('', $result['delivery']);
		$I->assertEquals('', $result['homeDeliveryCity']);
		$I->assertEquals('', $result['homeDeliveryStreet']);
		$I->assertEquals('', $result['homeDeliveryPostalCode']);
		$I->assertEquals('', $result['homeDeliveryCountry']);
	}

	public function billingAndDeliveryFieldsSetCorrectlyTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(1))
			->setBillingAddrCity('Prague')
			->setBillingAddrStreet('Wenceslas Square 1')
			->setBillingAddrPostalCode('11000')
			->setBillingAddrCountry('CZ')
			->setHomeDeliveryCity('Brno')
			->setHomeDeliveryStreet('Freedom Square 5')
			->setHomeDeliveryPostalCode('60200')
			->setHomeDeliveryCountry('CZ');

		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('Prague', $result['billingAddrCity']);
		$I->assertEquals('Wenceslas Square 1', $result['billingAddrStreet']);
		$I->assertEquals('11000', $result['billingAddrPostalCode']);
		$I->assertEquals('CZ', $result['billingAddrCountry']);
		$I->assertEquals('Brno', $result['homeDeliveryCity']);
		$I->assertEquals('Freedom Square 5', $result['homeDeliveryStreet']);
		$I->assertEquals('60200', $result['homeDeliveryPostalCode']);
		$I->assertEquals('CZ', $result['homeDeliveryCountry']);
	}

	// -----------------------------------------------------------------------
	// expirationTime optional
	// -----------------------------------------------------------------------

	public function expirationTimeDefaultsToEmptyStringTest(UnitTester $I): void
	{
		// expirationTime has default '' in $params, getExpirationTime() returns ''
		$payment = new Payment();
		$I->assertEquals('', $payment->getExpirationTime());
	}

	public function expirationTimePresentWhenSetTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(1))
			->setExpirationTime('2099-12-31T23:59:59');

		$result = (new PaymentCreateRequest($payment))->toArray();

		$I->assertEquals('2099-12-31T23:59:59', $result['expirationTime']);
	}
}
