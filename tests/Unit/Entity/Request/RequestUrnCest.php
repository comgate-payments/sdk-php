<?php

namespace Tests\Unit\Entity\Request;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\LangCode;
use Comgate\SDK\Entity\Codes\TypeCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Request\AboSingleTransferRequest;
use Comgate\SDK\Entity\Request\AppleDomainAssociationRequest;
use Comgate\SDK\Entity\Request\MethodsRequest;
use Comgate\SDK\Entity\Request\PaymentCancelRequest;
use Comgate\SDK\Entity\Request\PaymentStatusRequest;
use Comgate\SDK\Entity\Request\PreauthCancelRequest;
use Comgate\SDK\Entity\Request\PreauthCaptureRequest;
use Comgate\SDK\Entity\Request\SingleTransferRequest;
use Comgate\SDK\Entity\Request\TransferListRequest;
use DateTime;
use Tests\Support\UnitTester;

/**
 * Tests getUrn() for every Request class that builds a non-trivial URL.
 * All URL routing logic lives here — a silent wrong endpoint means a wrong API call.
 */
class RequestUrnCest
{
	// =========================================================================
	// MethodsRequest — most complex: up to 12 optional query parameters
	// =========================================================================

	public function testMethodsRequestMinimalUrnHasNoQueryString(UnitTester $I): void
	{
		$request = new MethodsRequest();
		$I->assertEquals('method.json', $request->getUrn());
	}

	public function testMethodsRequestTypeIsNotIncludedInUrn(UnitTester $I): void
	{
		$request = new MethodsRequest();
		$request->setType(TypeCode::TYPE_JSON);
		// 'type' must be stripped from the URN (it goes in the Accept header, not query)
		$I->assertStringNotContainsString('type=', $request->getUrn());
	}

	public function testMethodsRequestSingleOptionalParamAppearsInQuery(UnitTester $I): void
	{
		$request = new MethodsRequest();
		$request->setLang(LangCode::CS);

		$I->assertStringStartsWith('method.json?', $request->getUrn());
		$I->assertStringContainsString('lang=cs', $request->getUrn());
	}

	/**
	 * @return array<string, array{params: array<string, mixed>, expectedKeys: string[], absentKeys: string[]}>
	 */
	protected function methodsRequestOptionalParamScenarios(): array
	{
		return [
			'lang + curr' => [
				'params'      => ['lang' => LangCode::CS, 'curr' => CurrencyCode::CZK],
				'expectedKeys' => ['lang=cs', 'curr=CZK'],
				'absentKeys'   => ['country', 'price', 'initRecurring', 'type='],
			],
			'preauth + embedded' => [
				'params'      => ['preauth' => true, 'embedded' => false],
				'expectedKeys' => ['preauth=1', 'embedded='],
				'absentKeys'   => ['lang', 'curr', 'type='],
			],
			'chargeUnregulatedCardFees=true' => [
				'params'      => ['chargeUnregulatedCardFees' => true],
				'expectedKeys' => ['chargeUnregulatedCardFees=1'],
				'absentKeys'   => ['lang', 'type='],
			],
			'null params omitted' => [
				'params'      => ['lang' => null, 'curr' => null],
				'expectedKeys' => [],
				'absentKeys'   => ['lang', 'curr'],
			],
		];
	}

	#[DataProvider('methodsRequestOptionalParamScenarios')]
	public function testMethodsRequestOptionalParamsInUrn(UnitTester $I, Example $example): void
	{
		$request = new MethodsRequest();

		foreach ($example['params'] as $setter => $value) {
			$method = 'set' . ucfirst($setter);
			$request->$method($value);
		}

		$urn = $request->getUrn();

		foreach ($example['expectedKeys'] as $fragment) {
			$I->assertStringContainsString($fragment, $urn, "Expected '{$fragment}' in URN: {$urn}");
		}
		foreach ($example['absentKeys'] as $fragment) {
			$I->assertStringNotContainsString($fragment, $urn, "Expected '{$fragment}' absent in URN: {$urn}");
		}
	}

	// =========================================================================
	// TransferListRequest — date formatting and test flag
	// =========================================================================

	/**
	 * @return array<string, array{date: string, test: bool, expectedUrn: string}>
	 */
	protected function transferListUrnScenarios(): array
	{
		return [
			'specific date, test=false' => [
				'date'        => '2023-02-01',
				'test'        => false,
				'expectedUrn' => 'transferList/date/2023-02-01.json?test=false',
			],
			'specific date, test=true' => [
				'date'        => '2023-12-31',
				'test'        => true,
				'expectedUrn' => 'transferList/date/2023-12-31.json?test=true',
			],
		];
	}

	#[DataProvider('transferListUrnScenarios')]
	public function testTransferListRequestUrn(UnitTester $I, Example $example): void
	{
		$request = new TransferListRequest(new DateTime($example['date']), $example['test']);
		$I->assertEquals($example['expectedUrn'], $request->getUrn());
	}

	// =========================================================================
	// PaymentStatusRequest + PaymentCancelRequest — urlencode() on transId
	// =========================================================================

	/**
	 * @return array<string, array{transId: string, expectedFragment: string}>
	 */
	protected function transIdUrnScenarios(): array
	{
		return [
			'standard dashes' => [
				'transId'          => 'XXXX-YYYY-ZZZZ',
				'expectedFragment' => 'XXXX-YYYY-ZZZZ',
			],
			'transId with space (urlencode → +)' => [
				'transId'          => 'AB CD',
				'expectedFragment' => 'AB+CD',
			],
			'transId with slash (urlencode → %2F)' => [
				'transId'          => 'AB/CD',
				'expectedFragment' => 'AB%2FCD',
			],
		];
	}

	#[DataProvider('transIdUrnScenarios')]
	public function testPaymentStatusRequestUrn(UnitTester $I, Example $example): void
	{
		$request = new PaymentStatusRequest($example['transId']);
		$I->assertStringContainsString(
			$example['expectedFragment'],
			$request->getUrn(),
			"URN should contain encoded transId"
		);
		$I->assertStringStartsWith('payment/transId/', $request->getUrn());
		$I->assertStringEndsWith('.json', $request->getUrn());
	}

	#[DataProvider('transIdUrnScenarios')]
	public function testPaymentCancelRequestUrn(UnitTester $I, Example $example): void
	{
		$request = new PaymentCancelRequest($example['transId']);
		$I->assertStringContainsString($example['expectedFragment'], $request->getUrn());
		$I->assertStringStartsWith('payment/transId/', $request->getUrn());
	}

	#[DataProvider('transIdUrnScenarios')]
	public function testPreauthCancelRequestUrn(UnitTester $I, Example $example): void
	{
		$request = new PreauthCancelRequest($example['transId']);
		$I->assertStringContainsString($example['expectedFragment'], $request->getUrn());
		$I->assertStringStartsWith('preauth/transId/', $request->getUrn());
	}

	#[DataProvider('transIdUrnScenarios')]
	public function testPreauthCaptureRequestUrn(UnitTester $I, Example $example): void
	{
		$request = new PreauthCaptureRequest($example['transId'], Money::ofInt(10));
		$I->assertStringContainsString($example['expectedFragment'], $request->getUrn());
		$I->assertStringStartsWith('preauth/transId/', $request->getUrn());
		$I->assertStringEndsWith('.json', $request->getUrn());
	}

	// =========================================================================
	// SingleTransferRequest — integer ID + test flag in query string
	// =========================================================================

	/**
	 * @return array<string, array{id: int, test: bool, expectedUrn: string}>
	 */
	protected function singleTransferUrnScenarios(): array
	{
		return [
			'id=112233, test=true'  => ['id' => 112233, 'test' => true,  'expectedUrn' => 'singleTransfer/transferId/112233.json?test=true'],
			'id=1, test=false'      => ['id' => 1,      'test' => false, 'expectedUrn' => 'singleTransfer/transferId/1.json?test=false'],
		];
	}

	#[DataProvider('singleTransferUrnScenarios')]
	public function testSingleTransferRequestUrn(UnitTester $I, Example $example): void
	{
		$request = new SingleTransferRequest($example['id'], $example['test']);
		$I->assertEquals($example['expectedUrn'], $request->getUrn());
	}

	// =========================================================================
	// AboSingleTransferRequest — 4 params: type, encoding, test
	// =========================================================================

	/**
	 * @return array<string, array{type: string, encoding: string, test: bool, expectedFragment: string[]}>
	 */
	protected function aboSingleTransferUrnScenarios(): array
	{
		return [
			'v1 utf8 test=true' => [
				'type'             => AboSingleTransferRequest::ABO_TYPE_V1,
				'encoding'         => AboSingleTransferRequest::ABO_ENCODING_UTF8,
				'test'             => true,
				'expectedFragment' => ['type=v1', 'encoding=utf8', 'test=true'],
			],
			'v2 windows test=false' => [
				'type'             => AboSingleTransferRequest::ABO_TYPE_V2,
				'encoding'         => AboSingleTransferRequest::ABO_ENCODING_WINDOWS,
				'test'             => false,
				'expectedFragment' => ['type=v2', 'encoding=win1250', 'test=false'],
			],
		];
	}

	#[DataProvider('aboSingleTransferUrnScenarios')]
	public function testAboSingleTransferRequestUrn(UnitTester $I, Example $example): void
	{
		$request = new AboSingleTransferRequest(
			'112233',
			$example['test'],
			$example['type'],
			$example['encoding']
		);

		$urn = $request->getUrn();
		$I->assertStringStartsWith('aboSingleTransfer/transferId/112233.json?', $urn);

		foreach ($example['expectedFragment'] as $fragment) {
			$I->assertStringContainsString($fragment, $urn, "Expected '{$fragment}' in URN: {$urn}");
		}
	}

	// =========================================================================
	// AppleDomainAssociationRequest — currency is conditionally appended
	// =========================================================================

	public function testAppleDomainAssociationUrnWithNoCurrencyHasNoQueryString(UnitTester $I): void
	{
		$request = new AppleDomainAssociationRequest('', '');
		$I->assertEquals('appleDomainAssociation.json', $request->getUrn());
	}

	public function testAppleDomainAssociationUrnWithCurrencyAppendsCurrencyParam(UnitTester $I): void
	{
		$request = new AppleDomainAssociationRequest('APPLE_PAY', CurrencyCode::EUR);
		$urn = $request->getUrn();

		$I->assertStringContainsString('currency=EUR', $urn);
		// method is NOT a query param in the URN (only currency is conditional)
		$I->assertStringNotContainsString('method=', $urn);
	}

	public function testAppleDomainAssociationUrnMethodDoesNotAffectUrn(UnitTester $I): void
	{
		$noMethod  = new AppleDomainAssociationRequest('', CurrencyCode::EUR);
		$withMethod = new AppleDomainAssociationRequest('APPLE_PAY', CurrencyCode::EUR);

		// Both should produce identical URNs — method is irrelevant to URN construction
		$I->assertEquals($noMethod->getUrn(), $withMethod->getUrn());
	}
}
