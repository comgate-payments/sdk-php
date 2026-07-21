<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Refund;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\Api\PaymentNotFoundException;
use Comgate\SDK\Exception\Api\PreauthException;
use Comgate\SDK\Exception\ApiException;
use Tests\Support\Fixture\IntegrationApi;
use Tests\Support\IntegrationTester;

final class FixtureClientCest
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

	#[Group('payment')]
	#[Group('fixture')]
	public function paymentLifecycleUsesExpectedProtocol(IntegrationTester $I): void
	{
		$client = $this->api->client();
		$payment = $I->createPayment();
		$created = $client->createPayment($payment);

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

		$requests = $this->api->requests();
		$I->assertSame('POST', $requests[0]['method']);
		$I->assertSame('/payment.json', $requests[0]['path']);
		$I->assertSame('Basic ' . base64_encode('fixture-merchant:fixture-secret'), $requests[0]['headers']['Authorization']);
		$I->assertSame('SDK test payment', json_decode($requests[0]['body'], true)['label']);
		$I->assertSame('GET', $requests[1]['method']);
		$I->assertSame('/payment/transId/PAY-123.json', $requests[1]['path']);
		$I->assertSame('DELETE', $requests[2]['method']);
	}

	#[Group('payment')]
	#[Group('fixture')]
	public function paymentOperationsSerializeAndHydrate(IntegrationTester $I): void
	{
		$client = $this->api->client();
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

		$requests = $this->api->requests();
		$I->assertSame('PUT', $requests[0]['method']);
		$I->assertSame('/preauth/transId/PAY%2FA+B.json', $requests[0]['path']);
		$I->assertSame(500, json_decode($requests[0]['body'], true)['amount']);
		$I->assertSame('DELETE', $requests[1]['method']);
		$I->assertSame('/refund.json', $requests[2]['path']);
		$I->assertSame(250, json_decode($requests[2]['body'], true)['amount']);
		$I->assertSame('/recurring.json', $requests[3]['path']);
		$I->assertSame('/simulation.json', $requests[4]['path']);
	}

	#[Group('payment')]
	#[Group('fixture')]
	#[DataProvider('apiErrors')]
	public function mapsApiErrors(IntegrationTester $I, Example $example): void
	{
		$client = $this->api->client($example['mode']);
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
			'general API error' => ['mode' => 'api-error', 'operation' => 'status', 'exception' => ApiException::class],
			'payment not found' => ['mode' => 'missing', 'operation' => 'status', 'exception' => PaymentNotFoundException::class],
			'preauth rejected' => ['mode' => 'preauth-error', 'operation' => 'capture', 'exception' => PreauthException::class],
			'missing create parameter' => ['mode' => 'missing', 'operation' => 'create', 'exception' => MissingParamException::class],
		];
	}
}
