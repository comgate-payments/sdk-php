<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Entity\PaymentCard;
use Exception;
use Tests\Support\Fixture\IntegrationApi;
use Tests\Support\IntegrationTester;

final class FixtureMotoPaymentCest
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

	#[Group('moto')]
	#[Group('fixture')]
	public function encryptsCardDataBeforeSending(IntegrationTester $I): void
	{
		$payment = $I->createPayment();
		$card = new PaymentCard('4111111111111111', '203012', '123');
		$response = $this->api->client()->createMotoPayment($payment, $card);
		$I->assertSame('MOTO-1', $response->getTransId());
		$I->assertSame('PAID', $response->toArray()['status']);

		$body = json_decode($this->api->requests()[1]['body'], true);
		$I->assertSame('4111111111111111', $this->api->decrypt($body['encryptedCardNumber']));
		$I->assertSame('203012', $this->api->decrypt($body['encryptedCardExpiration']));
		$I->assertSame('123', $this->api->decrypt($body['encryptedCardCvv']));
		$I->assertSame('/pubCryptoKey.json', $this->api->requests()[0]['path']);
		$I->assertSame('/moto.json', $this->api->requests()[1]['path']);
	}

	#[Group('moto')]
	#[Group('fixture')]
	public function rejectsMissingPublicKey(IntegrationTester $I): void
	{
		$I->expectThrowable(new Exception('No public encryption key for encrypting card data'), function () use ($I): void {
			$this->api->client('no-key')->createMotoPayment($I->createPayment(), new PaymentCard('4111111111111111', '203012', '123'));
		});
	}

	#[Group('moto')]
	#[Group('fixture')]
	#[DataProvider('missingRequiredCardFields')]
	public function rejectsMissingRequiredCardFields(IntegrationTester $I, Example $example): void
	{
		$I->expectThrowable(new Exception($example['message']), function () use ($I, $example): void {
			$card = new PaymentCard($example['number'], $example['expiration'], '123');
			$this->api->client()->createMotoPayment($I->createPayment(), $card);
		});
	}

	protected function missingRequiredCardFields(): array
	{
		return [
			'card number' => ['number' => null, 'expiration' => '203012', 'message' => 'No card number for encrypting card data'],
			'expiration' => ['number' => '4111111111111111', 'expiration' => null, 'message' => 'No card expiration for encrypting card data'],
		];
	}

	#[Group('moto')]
	#[Group('fixture')]
	public function permitsMissingCvv(IntegrationTester $I): void
	{
		$response = $this->api->client()->createMotoPayment($I->createPayment(), new PaymentCard('4111111111111111', '203012'));
		$I->assertSame('MOTO-1', $response->getTransId());
		$I->assertArrayNotHasKey('encryptedCardCvv', json_decode($this->api->requests()[1]['body'], true));
	}
}
