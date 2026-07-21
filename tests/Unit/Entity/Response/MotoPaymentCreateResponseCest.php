<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\Group;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\MotoPaymentCreateResponse;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

#[Group('moto-response')]
class MotoPaymentCreateResponseCest
{
	public function successHydratesAllFieldsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'code'    => 0,
				'message' => 'OK',
				'transId' => 'MT12-CD34-EF56',
				'status'  => 'PENDING',
			]),
		]);

		$response = new MotoPaymentCreateResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('OK', $response->getMessage());
		$I->assertEquals('MT12-CD34-EF56', $response->getTransId());
		$I->assertEquals('PENDING', $response->getStatus());
	}

	public function code1400ThrowsMissingParamExceptionTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1400, 'message' => 'Missing required param']),
		]);

		$I->expectThrowable(MissingParamException::class, function () use ($mock) {
			new MotoPaymentCreateResponse($mock);
		});
	}

	public function defaultErrorThrowsApiExceptionTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Server error']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new MotoPaymentCreateResponse($mock);
		});
	}

	/**
	 * MotoPaymentCreateResponse has unique error message construction:
	 * on default error, it appends ' - {transId}' and ' - {status}' to message when present.
	 */
	public function defaultErrorAppendsTransIdAndStatusToMessageTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'code'    => 1500,
				'message' => 'Card declined',
				'transId' => 'MT99-FAIL',
				'status'  => 'CANCELLED',
			]),
		]);

		try {
			new MotoPaymentCreateResponse($mock);
			$I->fail('Expected ApiException to be thrown');
		} catch (ApiException $e) {
			$I->assertStringContainsString('Card declined', $e->getMessage());
			$I->assertStringContainsString('MT99-FAIL', $e->getMessage());
			$I->assertStringContainsString('CANCELLED', $e->getMessage());
		}
	}

	public function defaultErrorWithoutTransIdAndStatusOnlyUsesBaseMessageTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 9999, 'message' => 'Unknown error']),
		]);

		try {
			new MotoPaymentCreateResponse($mock);
			$I->fail('Expected ApiException to be thrown');
		} catch (ApiException $e) {
			$I->assertEquals('Unknown error', $e->getMessage());
		}
	}

	public function toArrayContainsAllFieldsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'code'    => 0,
				'message' => 'OK',
				'transId' => 'MT00-AA11',
				'status'  => 'PAID',
			]),
		]);

		$array = (new MotoPaymentCreateResponse($mock))->toArray();

		$I->assertArrayHasKey('code', $array);
		$I->assertArrayHasKey('message', $array);
		$I->assertArrayHasKey('transId', $array);
		$I->assertArrayHasKey('status', $array);
		$I->assertCount(4, $array);
	}
}
