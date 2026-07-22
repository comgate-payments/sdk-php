<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\Group;
use Comgate\SDK\ClientTerminal;
use Comgate\SDK\Entity\TerminalPayment;
use Comgate\SDK\Entity\TerminalRefund;
use Tests\Support\FakeTransport;
use Tests\Support\IntegrationTester;

/**
 * Drives {@see ClientTerminal} against a {@see FakeTransport} - one queued
 * response per terminal call.
 */
final class TerminalClientFunctionalCest
{
	#[Group('terminal')]
	public function paymentLifecycle(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())
			->willReturnJson(['code' => 0, 'message' => 'OK', 'transId' => 'TERM-PAY-1'])
			->willReturnJson([
				'code' => 0, 'message' => 'OK', 'price' => 1250, 'curr' => 'CZK', 'refId' => 'TP-1', 'transId' => 'TERM-PAY-1',
				'status' => 'PAID', 'fee' => '10', 'cardValid' => '12/30', 'cardNumber' => '****1111',
				'paymentErrorReason' => '', 'reversed' => false, 'amountRefunded' => '0',
			])
			->willReturnJson(['code' => 0, 'message' => 'OK']);
		$client = new ClientTerminal($transport);

		$created = $client->createPayment((new TerminalPayment())->setPrice(12.50)->setCurr('CZK')->setRefId('TP-1'));
		$I->assertSame('TERM-PAY-1', $created->toArray()['transId']);

		$status = $client->getPaymentStatus('TERM-PAY-1');
		$I->assertSame('PAID', $status->getStatus());
		$I->assertSame(1250, $status->toArray()['price']);
		$I->assertFalse($status->isReversed());
		$I->assertSame(0, $client->cancelPayment('TERM-PAY-1')->getCode());

		$requests = $transport->requests();
		$I->assertSame(['price' => 1250, 'curr' => 'CZK', 'refId' => 'TP-1'], $requests[0]['data']);
		$I->assertSame('terminalPayment/transId/TERM-PAY-1.json', $requests[1]['urn']);
		$I->assertSame('DELETE', $requests[2]['method']);
	}

	#[Group('terminal')]
	public function refundClosingAndStatusLifecycle(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())
			->willReturnJson(['code' => 0, 'message' => 'OK', 'transId' => 'TERM-REF-1'])
			->willReturnJson([
				'code' => 0, 'message' => 'OK', 'price' => 500, 'curr' => 'CZK', 'refId' => 'TR-1', 'transId' => 'TERM-REF-1',
				'status' => 'REFUNDED', 'cardNumber' => '****1111', 'reversed' => true,
			])
			->willReturnJson(['code' => 0, 'message' => 'OK'])
			->willReturnJson(['code' => 0, 'message' => 'OK', 'batchNumber' => 12, 'batchData' => [['count' => 2, 'amount' => 1750]]])
			->willReturnJson(['status' => 'ONLINE']);
		$client = new ClientTerminal($transport);

		$I->assertSame('TERM-REF-1', $client->createRefund((new TerminalRefund())->setPrice(5)->setCurr('CZK')->setRefId('TR-1'))->getTransId());

		$status = $client->getRefundStatus('TERM-REF-1');
		$I->assertSame('REFUNDED', $status->getStatus());
		$I->assertTrue($status->isReversed());
		$I->assertSame(0, $client->cancelRefund('TERM-REF-1')->getCode());

		$closing = $client->createClosing();
		$I->assertSame(12, $closing->getBatchNumber());
		$I->assertSame(2, $closing->toArray()['batchData'][0]['count']);
		$I->assertSame('ONLINE', $client->getTerminalStatus()->toArray()['status']);
	}
}
