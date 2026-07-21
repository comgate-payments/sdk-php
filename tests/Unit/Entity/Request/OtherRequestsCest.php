<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Request;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\LangCode;
use Comgate\SDK\Entity\Codes\CountryCode;
use Comgate\SDK\Entity\Codes\TypeCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Refund;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\Request\MethodsRequest;
use Comgate\SDK\Entity\Request\PaymentRefundRequest;
use Comgate\SDK\Entity\Request\RecurringPaymentRequest;
use Tests\Support\UnitTester;

class OtherRequestsCest
{
	// -----------------------------------------------------------------------
	// PaymentRefundRequest::toArray()
	// -----------------------------------------------------------------------

	#[Group('refund-request')]
	public function refundRequestMinimalParamsTest(UnitTester $I): void
	{
		$refund = (new Refund())
			->setTransId('AB12-CD34-EF56')
			->setAmount(Money::ofFloat(10.0));

		$result = (new PaymentRefundRequest($refund))->toArray();

		$I->assertEquals('AB12-CD34-EF56', $result['transId']);
		$I->assertEquals(1000, $result['amount']); // 10.0 CZK = 1000 cents
		$I->assertEquals('false', $result['test']); // default
		$I->assertEquals('', $result['refId']);      // default
	}

	#[Group('refund-request')]
	public function refundRequestWithAllParamsTest(UnitTester $I): void
	{
		$refund = (new Refund())
			->setTransId('ZZ99-AA00-BB11')
			->setAmount(Money::ofCents(500))
			->setTest(true)
			->setRefId('my-refund-ref');

		$result = (new PaymentRefundRequest($refund))->toArray();

		$I->assertEquals('ZZ99-AA00-BB11', $result['transId']);
		$I->assertEquals(500, $result['amount']);
		$I->assertEquals('true', $result['test']);
		$I->assertEquals('my-refund-ref', $result['refId']);
	}

	#[Group('refund-request')]
	public function refundRequestUrnTest(UnitTester $I): void
	{
		$refund = (new Refund())
			->setTransId('AB12')
			->setAmount(Money::ofInt(1));

		$I->assertEquals('refund.json', (new PaymentRefundRequest($refund))->getUrn());
	}

	// -----------------------------------------------------------------------
	// RecurringPaymentRequest::toArray()
	// -----------------------------------------------------------------------

	#[Group('recurring-request')]
	public function recurringRequestContainsRequiredFieldsTest(UnitTester $I): void
	{
		$payment = (new Payment())
			->setPrice(Money::ofInt(100))
			->setLabel('Recurring product')
			->setReferenceId('ref-recurring-01');
		$payment->setInitRecurringId('INIT-TRANS-ID');

		$result = (new RecurringPaymentRequest($payment))->toArray();

		$I->assertArrayHasKey('price', $result);
		$I->assertArrayHasKey('curr', $result);
		$I->assertArrayHasKey('label', $result);
		$I->assertArrayHasKey('refId', $result);
		$I->assertArrayHasKey('account', $result);
		$I->assertArrayHasKey('name', $result);
		$I->assertArrayHasKey('initRecurringId', $result);
		$I->assertArrayHasKey('test', $result);

		$I->assertEquals(10000, $result['price']); // 100 CZK = 10000 cents
		$I->assertEquals('Recurring product', $result['label']);
		$I->assertEquals('ref-recurring-01', $result['refId']);
		$I->assertEquals('INIT-TRANS-ID', $result['initRecurringId']);
	}

	#[Group('recurring-request')]
	public function recurringRequestUrnTest(UnitTester $I): void
	{
		$payment = (new Payment())->setPrice(Money::ofInt(1));
		$I->assertEquals('recurring.json', (new RecurringPaymentRequest($payment))->getUrn());
	}

	// -----------------------------------------------------------------------
	// MethodsRequest::toArray()
	// -----------------------------------------------------------------------

	#[Group('methods-request')]
	public function methodsRequestDefaultOnlyHasTypeTest(UnitTester $I): void
	{
		$result = (new MethodsRequest())->toArray();

		$I->assertArrayHasKey('type', $result);
		$I->assertEquals(TypeCode::TYPE_JSON, $result['type']);
		$I->assertCount(1, $result); // only 'type' — all optionals are null → absent
	}

	#[Group('methods-request')]
	public function methodsRequestNullableFieldsAbsentWhenNullTest(UnitTester $I): void
	{
		$result = (new MethodsRequest())->toArray();

		$I->assertArrayNotHasKey('lang', $result);
		$I->assertArrayNotHasKey('curr', $result);
		$I->assertArrayNotHasKey('country', $result);
		$I->assertArrayNotHasKey('price', $result);
		$I->assertArrayNotHasKey('initRecurring', $result);
		$I->assertArrayNotHasKey('verification', $result);
		$I->assertArrayNotHasKey('preauth', $result);
		$I->assertArrayNotHasKey('embedded', $result);
	}

	#[Group('methods-request')]
	public function methodsRequestWithAllParamsTest(UnitTester $I): void
	{
		$request = new MethodsRequest();
		$request->setType(TypeCode::TYPE_JSON);
		$request->setLang(LangCode::CS);
		$request->setCurr(CurrencyCode::CZK);
		$request->setCountry(CountryCode::CZ);
		$request->setPrice('10000');
		$request->setInitRecurring(true);
		$request->setPreauth(false);
		$request->setEmbedded(true);

		$result = $request->toArray();

		$I->assertEquals(LangCode::CS, $result['lang']);
		$I->assertEquals(CurrencyCode::CZK, $result['curr']);
		$I->assertEquals(CountryCode::CZ, $result['country']);
		$I->assertEquals('10000', $result['price']);
		$I->assertTrue($result['initRecurring']);
		$I->assertFalse($result['preauth']);
		$I->assertTrue($result['embedded']);
	}

	#[Group('methods-request')]
	public function methodsRequestGetUrnWithoutParamsTest(UnitTester $I): void
	{
		$urn = (new MethodsRequest())->getUrn();

		// No optional params → no query string (type is excluded from URL)
		$I->assertEquals('method.json', $urn);
	}

	#[Group('methods-request')]
	public function methodsRequestGetUrnWithParamsContainsQueryStringTest(UnitTester $I): void
	{
		$request = (new MethodsRequest())
			->setLang(LangCode::CS)
			->setCurr(CurrencyCode::CZK);

		$urn = $request->getUrn();

		$I->assertStringStartsWith('method.json?', $urn);
		$I->assertStringContainsString('lang=cs', $urn);
		$I->assertStringContainsString('curr=CZK', $urn);
	}
}
