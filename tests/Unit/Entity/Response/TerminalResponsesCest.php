<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\TerminalClosingResponse;
use Comgate\SDK\Entity\Response\TerminalPaymentCancelResponse;
use Comgate\SDK\Entity\Response\TerminalPaymentCreateResponse;
use Comgate\SDK\Entity\Response\TerminalPaymentStatusResponse;
use Comgate\SDK\Entity\Response\TerminalRefundCancelResponse;
use Comgate\SDK\Entity\Response\TerminalRefundCreateResponse;
use Comgate\SDK\Entity\Response\TerminalRefundStatusResponse;
use Comgate\SDK\Entity\Response\TerminalStatusResponse;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

#[Group('terminal-response')]
class TerminalResponsesCest
{
	// -----------------------------------------------------------------------
	// TerminalPaymentCreateResponse
	// -----------------------------------------------------------------------

	public function terminalPaymentCreateSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'AB12-CD34-EF56']),
		]);

		$response = new TerminalPaymentCreateResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('OK', $response->getMessage());
		$I->assertEquals('AB12-CD34-EF56', $response->getTransId());
	}

	public function terminalPaymentCreateErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Terminal error']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalPaymentCreateResponse($mock);
		});
	}

	public function terminalPaymentCreateMissingCodeDefaultsToErrorTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, ['getContent' => json_encode([])]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalPaymentCreateResponse($mock);
		});
	}

	// -----------------------------------------------------------------------
	// TerminalPaymentStatusResponse
	// -----------------------------------------------------------------------

	public function terminalPaymentStatusSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'code'               => 0,
				'message'            => 'OK',
				'price'              => 10000,
				'curr'               => 'CZK',
				'refId'              => 'order-42',
				'transId'            => 'AB12-CD34',
				'status'             => 'PAID',
				'fee'                => '1.50',
				'cardValid'          => '202512',
				'cardNumber'         => '****1234',
				'paymentErrorReason' => '',
				'reversed'           => false,
				'amountRefunded'     => '0',
			]),
		]);

		$response = new TerminalPaymentStatusResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals(10000, $response->getPrice());
		$I->assertEquals('CZK', $response->getCurr());
		$I->assertEquals('order-42', $response->getRefId());
		$I->assertEquals('AB12-CD34', $response->getTransId());
		$I->assertEquals('PAID', $response->getStatus());
		$I->assertEquals('1.50', $response->getFee());
		$I->assertEquals('202512', $response->getCardValid());
		$I->assertEquals('****1234', $response->getCardNumber());
		$I->assertFalse($response->isReversed());
		$I->assertEquals('0', $response->getAmountRefunded());
	}

	public function terminalPaymentStatusDefaultsForMissingFieldsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'OK']),
		]);

		$response = new TerminalPaymentStatusResponse($mock);

		$I->assertEquals(0, $response->getPrice());
		$I->assertEquals('', $response->getCurr());
		$I->assertEquals('', $response->getTransId());
		$I->assertEquals('', $response->getStatus());
		$I->assertFalse($response->isReversed());
	}

	public function terminalPaymentStatusErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1404, 'message' => 'Payment not found']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalPaymentStatusResponse($mock);
		});
	}

	public function terminalPaymentStatusToArrayTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'XY99', 'status' => 'PENDING']),
		]);

		$array = (new TerminalPaymentStatusResponse($mock))->toArray();

		$I->assertArrayHasKey('code', $array);
		$I->assertArrayHasKey('transId', $array);
		$I->assertArrayHasKey('status', $array);
		$I->assertArrayHasKey('reversed', $array);
		$I->assertArrayHasKey('amountRefunded', $array);
	}

	// -----------------------------------------------------------------------
	// TerminalPaymentCancelResponse
	// -----------------------------------------------------------------------

	public function terminalPaymentCancelSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'Cancelled']),
		]);

		$response = new TerminalPaymentCancelResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('Cancelled', $response->getMessage());
	}

	public function terminalPaymentCancelErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Cannot cancel']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalPaymentCancelResponse($mock);
		});
	}

	// -----------------------------------------------------------------------
	// TerminalClosingResponse
	// -----------------------------------------------------------------------

	public function terminalClosingSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'code'        => 0,
				'message'     => 'Closing done',
				'batchNumber' => 42,
				'batchData'   => [['amount' => 1000, 'count' => 5]],
			]),
		]);

		$response = new TerminalClosingResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals(42, $response->getBatchNumber());
		$I->assertCount(1, $response->getBatchData());
	}

	public function terminalClosingDefaultsForMissingBatchDataTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'OK']),
		]);

		$response = new TerminalClosingResponse($mock);

		$I->assertEquals(0, $response->getBatchNumber());
		$I->assertIsArray($response->getBatchData());
		$I->assertEmpty($response->getBatchData());
	}

	public function terminalClosingErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Terminal busy']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalClosingResponse($mock);
		});
	}

	// -----------------------------------------------------------------------
	// TerminalRefundCreateResponse
	// -----------------------------------------------------------------------

	public function terminalRefundCreateSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'RF12-CD34']),
		]);

		$response = new TerminalRefundCreateResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('RF12-CD34', $response->getTransId());
	}

	public function terminalRefundCreateErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Refund failed']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalRefundCreateResponse($mock);
		});
	}

	// -----------------------------------------------------------------------
	// TerminalRefundStatusResponse
	// -----------------------------------------------------------------------

	public function terminalRefundStatusSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'code'       => 0,
				'message'    => 'OK',
				'price'      => 5000,
				'curr'       => 'CZK',
				'refId'      => 'ref-99',
				'transId'    => 'RF99-AA11',
				'status'     => 'PAID',
				'cardNumber' => '****5678',
				'reversed'   => true,
			]),
		]);

		$response = new TerminalRefundStatusResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals(5000, $response->getPrice());
		$I->assertEquals('CZK', $response->getCurr());
		$I->assertEquals('RF99-AA11', $response->getTransId());
		$I->assertEquals('PAID', $response->getStatus());
		$I->assertEquals('****5678', $response->getCardNumber());
		$I->assertTrue($response->isReversed());
	}

	public function terminalRefundStatusErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1404, 'message' => 'Not found']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalRefundStatusResponse($mock);
		});
	}

	// -----------------------------------------------------------------------
	// TerminalRefundCancelResponse
	// -----------------------------------------------------------------------

	public function terminalRefundCancelSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'Refund cancelled']),
		]);

		$response = new TerminalRefundCancelResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('Refund cancelled', $response->getMessage());
	}

	public function terminalRefundCancelErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Cannot cancel refund']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new TerminalRefundCancelResponse($mock);
		});
	}

	// -----------------------------------------------------------------------
	// TerminalStatusResponse
	// -----------------------------------------------------------------------

	public function terminalStatusSuccessTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['status' => 'ONLINE']),
		]);

		$response = new TerminalStatusResponse($mock);

		$I->assertEquals('ONLINE', $response->getStatus());
	}

	public function terminalStatusDefaultsToUnknownWhenMissingTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, ['getContent' => json_encode([])]);

		$response = new TerminalStatusResponse($mock);

		$I->assertEquals('UNKNOWN', $response->getStatus());
	}

	public function terminalStatusToArrayTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['status' => 'OFFLINE']),
		]);

		$array = (new TerminalStatusResponse($mock))->toArray();

		$I->assertEquals(['status' => 'OFFLINE'], $array);
	}
}
