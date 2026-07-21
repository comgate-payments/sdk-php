<?php

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Stub;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Response\PaymentStatusResponse;
use Comgate\SDK\Exception\Api\PaymentNotFoundException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

class PaymentStatusResponseCest
{
	// ---------------------------------------------------------------------------
	// Helpers
	// ---------------------------------------------------------------------------

	/**
	 * Build a minimal valid success JSON payload, optionally overriding fields.
	 *
	 * @param array<string, mixed> $overrides
	 */
	private function buildSuccessJson(array $overrides = []): string
	{
		$defaults = [
			'code'               => 0,
			'message'            => 'OK',
			'merchant'           => 'merchant123',
			'secret'             => 'secretXYZ',
			'transId'            => 'XXXX-YYYY-ZZZZ',
			'test'               => 'false',
			'price'              => 10000,
			'curr'               => 'CZK',
			'label'              => 'Test payment',
			'refId'              => 'order-42',
			'payerId'            => 'payer-99',
			'method'             => 'CARD_CZ_COMGATE',
			'account'            => '',
			'email'              => 'buyer@example.com',
			'name'               => 'Widget',
			'phone'              => '+420000000000',
			'status'             => 'PENDING',
			'payerName'          => 'John Doe',
			'payerAcc'           => 'CZ00/0000',
			'fee'                => '0.00',
			'vs'                 => '123456',
			'cardValid'          => '12/27',
			'cardNumber'         => '****1234',
			'appliedFee'         => '0',
			'appliedFeeTyp'      => '',
			'paymentErrorReason' => '',
		];

		return (string) json_encode(array_merge($defaults, $overrides));
	}

	// ---------------------------------------------------------------------------
	// Full field hydration on success
	// ---------------------------------------------------------------------------

	public function testSuccessfulResponseHydratesAllFields(UnitTester $I): void
	{
		$responseMock = Stub::make(Response::class, [
			'getContent' => $this->buildSuccessJson(),
		]);

		$status = new PaymentStatusResponse($responseMock);

		$I->assertEquals(0, $status->getCode());
		$I->assertEquals('OK', $status->getMessage());
		$I->assertEquals('merchant123', $status->getMerchant());
		$I->assertEquals('secretXYZ', $status->getSecret());
		$I->assertEquals('XXXX-YYYY-ZZZZ', $status->getTransId());
		$I->assertEquals('CZK', $status->getCurrency());
		$I->assertEquals('Test payment', $status->getLabel());
		$I->assertEquals('order-42', $status->getRefId());
		$I->assertEquals('payer-99', $status->getPayerId());
		$I->assertEquals('CARD_CZ_COMGATE', $status->getMethod());
		$I->assertEquals('buyer@example.com', $status->getEmail());
		$I->assertEquals('Widget', $status->getName());
		$I->assertEquals('+420000000000', $status->getPhone());
		$I->assertEquals('PENDING', $status->getStatus());
		$I->assertEquals('John Doe', $status->getPayerName());
		$I->assertEquals('CZ00/0000', $status->getPayerAcc());
		$I->assertEquals('0.00', $status->getFee());
		$I->assertEquals('123456', $status->getVs());
		$I->assertEquals('12/27', $status->getCardValid());
		$I->assertEquals('****1234', $status->getCardNumber());
		$I->assertEquals('0', $status->getAppliedFee());
		$I->assertEquals('', $status->getAppliedFeeTyp());
		$I->assertEquals('', $status->getPaymentErrorReason());
	}

	// ---------------------------------------------------------------------------
	// Boolean coercion: JSON string 'true'/'false' → PHP bool
	// ---------------------------------------------------------------------------

	/**
	 * @return array<string, array{jsonTest: string, expectedBool: bool}>
	 */
	protected function testFlagScenarios(): array
	{
		return [
			'string "true" → bool true'  => ['jsonTest' => 'true',  'expectedBool' => true],
			'string "false" → bool false' => ['jsonTest' => 'false', 'expectedBool' => false],
		];
	}

	#[DataProvider('testFlagScenarios')]
	public function testTestFlagIsCoercedToBool(UnitTester $I, Example $example): void
	{
		$json = $this->buildSuccessJson(['test' => $example['jsonTest']]);
		$responseMock = Stub::make(Response::class, ['getContent' => $json]);

		$status = new PaymentStatusResponse($responseMock);
		$I->assertSame($example['expectedBool'], $status->isTest());
	}

	// ---------------------------------------------------------------------------
	// Price in cents → Money value object
	// ---------------------------------------------------------------------------

	/**
	 * @return array<string, array{priceCents: int, expectedReal: float}>
	 */
	protected function priceConversionScenarios(): array
	{
		return [
			'100 CZK (10000 cents)' => ['priceCents' => 10000, 'expectedReal' => 100.0],
			'1 cent'                => ['priceCents' => 1,     'expectedReal' => 0.01],
			'0 (free)'              => ['priceCents' => 0,     'expectedReal' => 0.0],
			'large amount'          => ['priceCents' => 999999,'expectedReal' => 9999.99],
		];
	}

	#[DataProvider('priceConversionScenarios')]
	public function testPriceIsHydratedAsMoney(UnitTester $I, Example $example): void
	{
		$json = $this->buildSuccessJson(['price' => $example['priceCents']]);
		$responseMock = Stub::make(Response::class, ['getContent' => $json]);

		$status = new PaymentStatusResponse($responseMock);
		$I->assertInstanceOf(Money::class, $status->getPrice());
		$I->assertEquals($example['priceCents'], $status->getPrice()->get());
		$I->assertEquals($example['expectedReal'], $status->getPrice()->getReal());
	}

	// ---------------------------------------------------------------------------
	// Missing optional fields fall back to empty-string defaults
	// ---------------------------------------------------------------------------

	public function testMissingOptionalFieldsFallBackToEmptyString(UnitTester $I): void
	{
		// Provide only the mandatory fields; every optional field is absent
		$json = (string) json_encode([
			'code'    => 0,
			'message' => 'OK',
			'transId' => 'T-001',
		]);
		$responseMock = Stub::make(Response::class, ['getContent' => $json]);

		$status = new PaymentStatusResponse($responseMock);

		$I->assertEquals('', $status->getMerchant());
		$I->assertEquals('', $status->getSecret());
		$I->assertEquals('', $status->getCurrency());
		$I->assertEquals('', $status->getLabel());
		$I->assertEquals('', $status->getRefId());
		$I->assertEquals('', $status->getPayerId());
		$I->assertEquals('', $status->getMethod());
		$I->assertEquals('', $status->getAccount());
		$I->assertEquals('', $status->getEmail());
		$I->assertEquals('', $status->getName());
		$I->assertEquals('', $status->getPhone());
		$I->assertEquals('', $status->getStatus());
		$I->assertEquals('', $status->getPayerName());
		$I->assertEquals('', $status->getPayerAcc());
		$I->assertEquals('', $status->getFee());
		$I->assertEquals('', $status->getVs());
		$I->assertEquals('', $status->getCardValid());
		$I->assertEquals('', $status->getCardNumber());
		$I->assertEquals('', $status->getAppliedFee());
		$I->assertEquals('', $status->getAppliedFeeTyp());
		$I->assertEquals('', $status->getPaymentErrorReason());
		// 'test' absent → treated as 'false' → false
		$I->assertFalse($status->isTest());
		// 'price' absent → 0 cents
		$I->assertEquals(0, $status->getPrice()->get());
	}

	// ---------------------------------------------------------------------------
	// Error code dispatch
	// ---------------------------------------------------------------------------

	/**
	 * @return array<string, array{code: int, message: string, expectedException: class-string<\Throwable>}>
	 */
	protected function errorCodeScenarios(): array
	{
		return [
			'code 1400 → PaymentNotFoundException' => [
				'code'              => 1400,
				'message'           => 'Transaction not found',
				'expectedException' => PaymentNotFoundException::class,
			],
			'code 1401 → generic ApiException'     => [
				'code'              => 1401,
				'message'           => 'Unauthorized',
				'expectedException' => ApiException::class,
			],
			'code 1500 → generic ApiException'     => [
				'code'              => 1500,
				'message'           => 'Internal server error',
				'expectedException' => ApiException::class,
			],
			'code 9999 → generic ApiException'     => [
				'code'              => 9999,
				'message'           => 'Unknown',
				'expectedException' => ApiException::class,
			],
		];
	}

	#[DataProvider('errorCodeScenarios')]
	public function testErrorCodesThrowCorrectExceptions(UnitTester $I, Example $example): void
	{
		$json = json_encode([
			'code'    => $example['code'],
			'message' => $example['message'],
		]);
		$responseMock = Stub::make(Response::class, ['getContent' => $json]);

		$I->expectThrowable($example['expectedException'], function () use ($responseMock) {
			new PaymentStatusResponse($responseMock);
		});
	}

	/**
	 * `PaymentNotFoundException` extends `ApiException`.
	 * For code 1400 the thrown exception must be the specific subtype, not just
	 * the generic parent — verifies correct exception hierarchy is preserved.
	 */
	public function testCode1400ThrowsPaymentNotFoundAndNotGenericApiException(UnitTester $I): void
	{
		$json = json_encode(['code' => 1400, 'message' => 'Not found']);
		$responseMock = Stub::make(Response::class, ['getContent' => $json]);

		try {
			new PaymentStatusResponse($responseMock);
			$I->fail('Expected PaymentNotFoundException to be thrown');
		} catch (PaymentNotFoundException $e) {
			$I->assertInstanceOf(ApiException::class, $e); // is-a ApiException
			$I->assertEquals(1400, $e->getCode());
		}
	}
}
