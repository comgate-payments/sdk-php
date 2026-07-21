<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\TerminalPayment;
use Comgate\SDK\Entity\TerminalRefund;
use Tests\Support\Fixture\IntegrationApi;
use Tests\Support\IntegrationTester;

final class FixtureTerminalClientCest
{
	/** @var IntegrationApi */
	private $api;

	public function _before(): void
	{
		$this->api = new IntegrationApi();
		$this->api->start();
	}

	public function _after(): void
	{
		$this->api->stop();
	}

	#[Group('terminal')]
	#[Group('fixture')]
	public function paymentLifecycle(IntegrationTester $I): void
	{
		$client = $this->api->terminalClient();
		$payment = (new TerminalPayment())->setPrice(12.50)->setCurr('CZK')->setRefId('TP-1');
		$created = $client->createPayment($payment);
		$I->assertSame('TERM-PAY-1', $created->toArray()['transId']);

		$status = $client->getPaymentStatus('TERM-PAY-1');
		$I->assertSame('PAID', $status->getStatus());
		$I->assertSame(1250, $status->toArray()['price']);
		$I->assertFalse($status->isReversed());
		$I->assertSame(0, $client->cancelPayment('TERM-PAY-1')->getCode());

		$requests = $this->api->requests();
		$I->assertSame(['price' => 1250, 'curr' => 'CZK', 'refId' => 'TP-1'], json_decode($requests[0]['body'], true));
		$I->assertSame('/terminalPayment/transId/TERM-PAY-1.json', $requests[1]['path']);
		$I->assertSame('DELETE', $requests[2]['method']);
	}

	#[Group('terminal')]
	#[Group('fixture')]
	public function refundClosingAndStatusLifecycle(IntegrationTester $I): void
	{
		$client = $this->api->terminalClient();
		$refund = (new TerminalRefund())->setPrice(5)->setCurr('CZK')->setRefId('TR-1');
		$I->assertSame('TERM-REF-1', $client->createRefund($refund)->getTransId());

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
