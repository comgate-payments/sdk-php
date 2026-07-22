<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Client;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Refund;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\Api\PaymentNotFoundException;
use Comgate\SDK\Exception\Api\PreauthException;
use Comgate\SDK\Exception\ApiException;
use Tests\Support\FakeTransport;
use Tests\Support\IntegrationTester;

/**
 *
 * Drives {@see Client} against a {@see FakeTransport}: each test queues the
 * API responses it expects and then asserts both the hydrated result and the
 * requests the SDK sent.
 */
final class ClientFunctionalCest
{
	#[Group('payment')]
	public function paymentLifecycleUsesExpectedProtocol(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())
			->willReturnJson(['code' => 0, 'message' => 'OK', 'transId' => 'PAY-123', 'redirect' => 'https://pay.test/PAY-123'])
			->willReturnJson([
				'code' => 0, 'message' => 'OK', 'merchant' => 'fixture-merchant', 'secret' => 'fixture-secret',
				'transId' => 'PAY-123', 'test' => 'true', 'price' => 12345, 'curr' => 'CZK', 'label' => 'Fixture order',
				'refId' => 'ORDER-42', 'payerId' => 'PAYER-7', 'method' => 'CARD_CZ_CSOB_2', 'account' => '123/0100',
				'email' => 'buyer@example.test', 'name' => 'Ada Buyer', 'phone' => '+420123456789', 'status' => 'PAID',
				'payerName' => 'Ada Buyer', 'payerAcc' => '987/0300', 'fee' => '12', 'vs' => '42', 'cardValid' => '12/30',
				'cardNumber' => '************1111', 'appliedFee' => '3', 'appliedFeeTyp' => 'fixed', 'paymentErrorReason' => '',
			])
			->willReturnJson(['code' => 0, 'message' => 'OK']);
		$client = new Client($transport);

		$created = $client->createPayment($I->createPayment());
		$I->assertSame('PAY-123', $created->getTransId());
		$I->assertSame('https://pay.test/PAY-123', $created->getRedirect());
		$I->assertSame('PAY-123', $created->toArray()['transId']);

		$status = $client->getStatus($created->getTransId());
		$I->assertTrue($status->isTest());
		$I->assertSame(12345, $status->getPrice()->get());
		$I->assertSame('CARD_CZ_CSOB_2', $status->getMethod());
		$I->assertSame('************1111', $status->getCardNumber());
		$I->assertSame('fixed', $status->toArray()['appliedFeeTyp']);

		$cancelled = $client->cancelPayment($created->getTransId());
		$I->assertSame(['code' => 0, 'message' => 'OK'], $cancelled->toArray());

		$requests = $transport->requests();
		$I->assertSame('POST', $requests[0]['method']);
		$I->assertSame('payment.json', $requests[0]['urn']);
		$I->assertSame('SDK test payment', $requests[0]['data']['label']);
		$I->assertSame('GET', $requests[1]['method']);
		$I->assertSame('payment/transId/PAY-123.json', $requests[1]['urn']);
		$I->assertSame('DELETE', $requests[2]['method']);
	}

	#[Group('payment')]
	public function paymentOperationsSerializeAndHydrate(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())
			->willReturnJson(['code' => 0, 'message' => 'OK'])
			->willReturnJson(['code' => 0, 'message' => 'OK'])
			->willReturnJson(['code' => 0, 'message' => 'OK'])
			->willReturnJson(['code' => 0, 'message' => 'OK', 'transId' => 'REC-123'])
			->willReturnJson(['code' => 0, 'message' => 'OK']);
		$client = new Client($transport);

		$I->assertSame(0, $client->capturePreauth('PAY/A B', Money::ofCents(500))->getCode());
		$I->assertSame(0, $client->cancelPreauth('PAY/A B')->getCode());

		$refund = (new Refund())
			->setTransId('PAY-123')
			->setAmount(Money::ofCents(250))
			->setCurrency(CurrencyCode::CZK)
			->setTest(true)
			->setRefId('REF-1');
		$I->assertSame(0, $client->refundPayment($refund)->getCode());

		$recurring = $I->createPayment();
		$recurring->setInitRecurringId('PAY-123');
		$I->assertSame('REC-123', $client->initRecurringPayment($recurring)->getTransId());
		$I->assertSame(0, $client->simulation(['subject' => 'payment', 'transId' => 'PAY-123', 'task' => 'PAID'])->getCode());

		$requests = $transport->requests();
		$I->assertSame('PUT', $requests[0]['method']);
		$I->assertSame('preauth/transId/PAY%2FA+B.json', $requests[0]['urn']);
		$I->assertSame(500, $requests[0]['data']['amount']);
		$I->assertSame('DELETE', $requests[1]['method']);
		$I->assertSame('refund.json', $requests[2]['urn']);
		$I->assertSame(250, $requests[2]['data']['amount']);
		$I->assertSame('recurring.json', $requests[3]['urn']);
		$I->assertSame('simulation.json', $requests[4]['urn']);
	}

	#[Group('payment')]
	#[DataProvider('apiErrors')]
	public function mapsApiErrors(IntegrationTester $I, Example $example): void
	{
		$client = new Client((new FakeTransport())->willReturnJson($example['response']));

		$I->expectThrowable($example['exception'], function () use ($I, $client, $example): void {
			if ($example['operation'] === 'status') {
				$client->getStatus('missing');
				return;
			}
			if ($example['operation'] === 'capture') {
				$client->capturePreauth('PAY-1', Money::ofCents(100));
				return;
			}
			$client->createPayment($I->createPayment());
		});
	}

	protected function apiErrors(): array
	{
		return [
			'general API error' => ['response' => ['code' => 1500, 'message' => 'Fixture API error'], 'operation' => 'status', 'exception' => ApiException::class],
			'payment not found' => ['response' => ['code' => 1400, 'message' => 'Fixture missing parameter'], 'operation' => 'status', 'exception' => PaymentNotFoundException::class],
			'preauth rejected' => ['response' => ['code' => 1401, 'message' => 'Fixture preauth error'], 'operation' => 'capture', 'exception' => PreauthException::class],
			'missing create parameter' => ['response' => ['code' => 1400, 'message' => 'Fixture missing parameter'], 'operation' => 'create', 'exception' => MissingParamException::class],
		];
	}
}
