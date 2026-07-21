<?php

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\PaymentCancelResponse;
use Comgate\SDK\Entity\Response\RecurringPaymentResponse;
use Comgate\SDK\Entity\Response\RefundResponse;
use Comgate\SDK\Entity\Response\SimulationResponse;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

/**
 * Tests for four "simple" response classes that share the same 2-field
 * (code + message) dispatch pattern.
 *
 * Notable differences between the four:
 *  - RecurringPaymentResponse  → also stores transId from JSON
 *  - SimulationResponse        → has NO explicit 1400 branch; code 1400
 *                                falls through to the default ApiException,
 *                                NOT MissingParamException.
 */
class SimpleResponsesCest
{
	// -----------------------------------------------------------------------
	// Success path — all four classes
	// -----------------------------------------------------------------------

	/**
	 * @return array<string, array{class: class-string, payload: array<string,mixed>}>
	 */
	protected function successScenarios(): array
	{
		return [
			'PaymentCancelResponse success' => [
				'class'   => PaymentCancelResponse::class,
				'payload' => ['code' => 0, 'message' => 'Payment cancelled'],
			],
			'RecurringPaymentResponse success' => [
				'class'   => RecurringPaymentResponse::class,
				'payload' => ['code' => 0, 'message' => 'OK', 'transId' => 'AB12-CD34-EF56'],
			],
			'RefundResponse success' => [
				'class'   => RefundResponse::class,
				'payload' => ['code' => 0, 'message' => 'Refund accepted'],
			],
			'SimulationResponse success' => [
				'class'   => SimulationResponse::class,
				'payload' => ['code' => 0, 'message' => 'Simulation done'],
			],
		];
	}

	#[DataProvider('successScenarios')]
	public function testSuccessPopulatesCodeAndMessage(UnitTester $I, Example $example): void
	{
		$mock = Stub::make(Response::class, ['getContent' => json_encode($example['payload'])]);

		$class    = $example['class'];
		$response = new $class($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals($example['payload']['message'], $response->getMessage());
	}

	// -----------------------------------------------------------------------
	// RecurringPaymentResponse — transId hydration
	// -----------------------------------------------------------------------

	public function testRecurringPaymentResponseHydratesTransId(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'ABCD-1234-EFGH']),
		]);

		$response = new RecurringPaymentResponse($mock);

		$I->assertEquals('ABCD-1234-EFGH', $response->getTransId());
		$I->assertSame(
			['code' => 0, 'message' => 'OK', 'transId' => 'ABCD-1234-EFGH'],
			$response->toArray(),
		);
	}

	// -----------------------------------------------------------------------
	// Code 1400 → MissingParamException on the three classes that handle it
	// -----------------------------------------------------------------------

	/**
	 * @return array<string, array{class: class-string}>
	 */
	protected function missingParamScenarios(): array
	{
		return [
			'PaymentCancelResponse 1400'   => ['class' => PaymentCancelResponse::class],
			'RecurringPaymentResponse 1400' => ['class' => RecurringPaymentResponse::class],
			'RefundResponse 1400'           => ['class' => RefundResponse::class],
		];
	}

	#[DataProvider('missingParamScenarios')]
	public function testCode1400ThrowsMissingParamException(UnitTester $I, Example $example): void
	{
		$mock  = Stub::make(Response::class, ['getContent' => json_encode(['code' => 1400, 'message' => 'Missing transId'])]);
		$class = $example['class'];

		$I->expectThrowable(MissingParamException::class, function () use ($class, $mock) {
			new $class($mock);
		});
	}

	// -----------------------------------------------------------------------
	// SimulationResponse — code 1400 has NO dedicated branch
	// Falls through to the default → ApiException, NOT MissingParamException
	// -----------------------------------------------------------------------

	public function testSimulationResponseCode1400ThrowsApiExceptionNotMissingParam(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, ['getContent' => json_encode(['code' => 1400, 'message' => 'Error'])]);

		try {
			new SimulationResponse($mock);
			$I->fail('Expected ApiException to be thrown');
		} catch (ApiException $e) {
			$I->assertNotInstanceOf(
				MissingParamException::class,
				$e,
				'SimulationResponse has no explicit 1400 branch; code 1400 must fall through to ApiException',
			);
			$I->assertEquals(1400, $e->getCode());
		}
	}

	// -----------------------------------------------------------------------
	// Generic error code → ApiException on all four classes
	// -----------------------------------------------------------------------

	/**
	 * @return array<string, array{class: class-string}>
	 */
	protected function genericErrorScenarios(): array
	{
		return [
			'PaymentCancelResponse 9999'    => ['class' => PaymentCancelResponse::class],
			'RecurringPaymentResponse 9999' => ['class' => RecurringPaymentResponse::class],
			'RefundResponse 9999'           => ['class' => RefundResponse::class],
			'SimulationResponse 9999'       => ['class' => SimulationResponse::class],
		];
	}

	#[DataProvider('genericErrorScenarios')]
	public function testGenericErrorCodeThrowsApiException(UnitTester $I, Example $example): void
	{
		$mock  = Stub::make(Response::class, ['getContent' => json_encode(['code' => 9999, 'message' => 'Unknown'])]);
		$class = $example['class'];

		$I->expectThrowable(ApiException::class, function () use ($class, $mock) {
			new $class($mock);
		});
	}

	// -----------------------------------------------------------------------
	// toArray() shape — verify all fields are present for each class
	// -----------------------------------------------------------------------

	/**
	 * @return array<string, array{class: class-string, payload: array<string,mixed>, expectedKeys: list<string>}>
	 */
	protected function toArrayScenarios(): array
	{
		return [
			'PaymentCancelResponse toArray' => [
				'class'        => PaymentCancelResponse::class,
				'payload'      => ['code' => 0, 'message' => 'Cancelled'],
				'expectedKeys' => ['code', 'message'],
			],
			'RecurringPaymentResponse toArray' => [
				'class'        => RecurringPaymentResponse::class,
				'payload'      => ['code' => 0, 'message' => 'OK', 'transId' => 'XY99'],
				'expectedKeys' => ['code', 'message', 'transId'],
			],
			'RefundResponse toArray' => [
				'class'        => RefundResponse::class,
				'payload'      => ['code' => 0, 'message' => 'Refunded'],
				'expectedKeys' => ['code', 'message'],
			],
			'SimulationResponse toArray' => [
				'class'        => SimulationResponse::class,
				'payload'      => ['code' => 0, 'message' => 'Simulated'],
				'expectedKeys' => ['code', 'message'],
			],
		];
	}

	#[DataProvider('toArrayScenarios')]
	public function testToArrayContainsExpectedKeys(UnitTester $I, Example $example): void
	{
		$mock     = Stub::make(Response::class, ['getContent' => json_encode($example['payload'])]);
		$class    = $example['class'];
		$response = new $class($mock);
		$array    = $response->toArray();

		foreach ($example['expectedKeys'] as $key) {
			$I->assertArrayHasKey($key, $array);
		}

		$I->assertCount(count($example['expectedKeys']), $array);
	}
}
