<?php

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\PreauthCancelResponse;
use Comgate\SDK\Entity\Response\PreauthCaptureResponse;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\Api\PreauthException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

/**
 * Tests for PreauthCaptureResponse and PreauthCancelResponse.
 *
 * Both classes share identical switch-dispatch logic:
 *   code 0    → success
 *   code 1400 → MissingParamException
 *   code 1401 → PreauthException   ← only place in SDK this is thrown
 *   default   → ApiException
 *
 * PreauthCaptureResponse additionally exposes toArray().
 */
class PreauthResponsesCest
{
	// -----------------------------------------------------------------------
	// Success path — both classes
	// -----------------------------------------------------------------------

	/**
	 * @return array<string, array{class: class-string, code: int, message: string}>
	 */
	protected function successScenarios(): array
	{
		return [
			'PreauthCaptureResponse success' => [
				'class'   => PreauthCaptureResponse::class,
				'code'    => 0,
				'message' => 'OK',
			],
			'PreauthCancelResponse success' => [
				'class'   => PreauthCancelResponse::class,
				'code'    => 0,
				'message' => 'Payment preauth cancelled',
			],
		];
	}

	#[DataProvider('successScenarios')]
	public function testSuccessPopulatesCodeAndMessage(UnitTester $I, Example $example): void
	{
		$json = json_encode(['code' => $example['code'], 'message' => $example['message']]);
		$mock = Stub::make(Response::class, ['getContent' => $json]);

		$class    = $example['class'];
		$response = new $class($mock);

		$I->assertEquals($example['code'], $response->getCode());
		$I->assertEquals($example['message'], $response->getMessage());
	}

	// -----------------------------------------------------------------------
	// Error code dispatch — both classes × 3 codes
	// -----------------------------------------------------------------------

	/**
	 * @return array<string, array{class: class-string, code: int, expectedException: class-string<\Throwable>}>
	 */
	protected function errorDispatchScenarios(): array
	{
		$cases = [];
		foreach ([PreauthCaptureResponse::class, PreauthCancelResponse::class] as $class) {
			$shortName = (new \ReflectionClass($class))->getShortName();

			$cases["{$shortName} code 1400 → MissingParamException"] = [
				'class'             => $class,
				'code'              => 1400,
				'message'           => 'Required parameter missing',
				'expectedException' => MissingParamException::class,
			];
			$cases["{$shortName} code 1401 → PreauthException"] = [
				'class'             => $class,
				'code'              => 1401,
				'message'           => 'Preauth operation failed',
				'expectedException' => PreauthException::class,
			];
			$cases["{$shortName} code 9999 → ApiException"] = [
				'class'             => $class,
				'code'              => 9999,
				'message'           => 'Unexpected error',
				'expectedException' => ApiException::class,
			];
		}

		return $cases;
	}

	#[DataProvider('errorDispatchScenarios')]
	public function testErrorCodeDispatchThrowsCorrectException(UnitTester $I, Example $example): void
	{
		$json = json_encode(['code' => $example['code'], 'message' => $example['message']]);
		$mock = Stub::make(Response::class, ['getContent' => $json]);

		$class = $example['class'];

		$I->expectThrowable($example['expectedException'], function () use ($class, $mock) {
			new $class($mock);
		});
	}

	// -----------------------------------------------------------------------
	// PreauthCaptureResponse::toArray()
	// -----------------------------------------------------------------------

	public function testCaptureToArrayReflectsStoredValues(UnitTester $I): void
	{
		$json = json_encode(['code' => 0, 'message' => 'Captured']);
		$mock = Stub::make(Response::class, ['getContent' => $json]);

		$response = new PreauthCaptureResponse($mock);
		$array    = $response->toArray();

		$I->assertSame(['code' => 0, 'message' => 'Captured'], $array);
	}

	// -----------------------------------------------------------------------
	// Exception hierarchy — PreauthException specifics
	// -----------------------------------------------------------------------

	/**
	 * PreauthException must extend ApiException so generic catch blocks work.
	 */
	public function testPreauthExceptionExtendsApiException(UnitTester $I): void
	{
		$e = new PreauthException('test', 1401);

		$I->assertInstanceOf(ApiException::class, $e);
	}

	/**
	 * Code 1401 must throw PreauthException, NOT MissingParamException.
	 * Guards against a future refactor that widens or swaps the exception type.
	 */
	public function testCode1401ThrowsPreauthExceptionNotMissingParam(UnitTester $I): void
	{
		$json = json_encode(['code' => 1401, 'message' => 'Preauth failed']);
		$mock = Stub::make(Response::class, ['getContent' => $json]);

		try {
			new PreauthCaptureResponse($mock);
			$I->fail('Expected PreauthException to be thrown');
		} catch (ApiException $e) {
			$I->assertInstanceOf(PreauthException::class, $e);
			$I->assertNotInstanceOf(MissingParamException::class, $e);
			$I->assertEquals(1401, $e->getCode());
			$I->assertEquals('Preauth failed', $e->getMessage());
		}
	}

	/**
	 * Code 1400 must throw MissingParamException, NOT PreauthException.
	 * Guards against accidentally swapping the two exception types.
	 */
	public function testCode1400ThrowsMissingParamNotPreauthException(UnitTester $I): void
	{
		$json = json_encode(['code' => 1400, 'message' => 'Missing transId']);
		$mock = Stub::make(Response::class, ['getContent' => $json]);

		try {
			new PreauthCancelResponse($mock);
			$I->fail('Expected MissingParamException to be thrown');
		} catch (ApiException $e) {
			$I->assertInstanceOf(MissingParamException::class, $e);
			$I->assertNotInstanceOf(PreauthException::class, $e);
			$I->assertEquals(1400, $e->getCode());
		}
	}
}
