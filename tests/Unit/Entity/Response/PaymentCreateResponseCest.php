<?php

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\PaymentCreateResponse;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

class PaymentCreateResponseCest
{
	public function testSuccessfulResponseParsesTransIdAndRedirect(UnitTester $I): void
	{
		$json = json_encode([
			'code'     => 0,
			'message'  => 'OK',
			'transId'  => 'ABCD-1234-EFGH',
			'redirect' => 'https://payments.comgate.cz/client/instructions/index?id=ABCD-1234-EFGH',
		]);

		$responseMock = Stub::make(Response::class, ['getContent' => $json]);
		$response = new PaymentCreateResponse($responseMock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('OK', $response->getMessage());
		$I->assertEquals('ABCD-1234-EFGH', $response->getTransId());
		$I->assertStringContainsString('ABCD-1234-EFGH', $response->getRedirect());
	}

	// ---------------------------------------------------------------------------
	// Error code dispatch — each non-zero code must throw the correct exception
	// ---------------------------------------------------------------------------

	/**
	 * @return array<string, array{code: int, message: string, expectedException: class-string<\Throwable>}>
	 */
	protected function errorCodeScenarios(): array
	{
		return [
			'code 1400 → MissingParamException'  => [
				'code'              => 1400,
				'message'           => 'Missing required parameter',
				'expectedException' => MissingParamException::class,
			],
			'code 1401 → generic ApiException'   => [
				'code'              => 1401,
				'message'           => 'Unauthorized',
				'expectedException' => ApiException::class,
			],
			'code 1500 → generic ApiException'   => [
				'code'              => 1500,
				'message'           => 'Internal error',
				'expectedException' => ApiException::class,
			],
			'code 9999 → generic ApiException'   => [
				'code'              => 9999,
				'message'           => 'Unknown error',
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
			new PaymentCreateResponse($responseMock);
		});
	}

	/**
	 * MissingParamException must NOT be thrown for a generic non-1400 error;
	 * i.e., the exception hierarchy must not accidentally widen the catch.
	 */
	public function testCode1401DoesNotThrowMissingParamException(UnitTester $I): void
	{
		$json = json_encode(['code' => 1401, 'message' => 'Unauthorized']);
		$responseMock = Stub::make(Response::class, ['getContent' => $json]);

		try {
			new PaymentCreateResponse($responseMock);
			$I->fail('Expected ApiException to be thrown');
		} catch (ApiException $e) {
			$I->assertNotInstanceOf(MissingParamException::class, $e);
		}
	}
}
