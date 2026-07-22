<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Client;
use Comgate\SDK\Entity\PaymentCard;
use Exception;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PrivateKey;
use Tests\Support\FakeTransport;
use Tests\Support\IntegrationTester;

/**
 * Covers MOTO card encryption without a live crypto endpoint: the test owns the
 * RSA key pair, hands the SDK the matching public JWK via {@see FakeTransport},
 * and decrypts what the SDK sent to prove the card data was encrypted correctly.
 */
final class MotoPaymentFunctionalCest
{
	/** @var PrivateKey */
	private $privateKey;

	#[Group('moto')]
	public function encryptsCardDataBeforeSending(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())
			->willReturnJson($this->publicKeyResponse())
			->willReturnJson(['code' => 0, 'message' => 'OK', 'transId' => 'MOTO-1', 'status' => 'PAID']);

		$card = new PaymentCard('4111111111111111', '203012', '123');
		$response = (new Client($transport))->createMotoPayment($I->createPayment(), $card);
		$I->assertSame('MOTO-1', $response->getTransId());
		$I->assertSame('PAID', $response->toArray()['status']);

		$requests = $transport->requests();
		$body = $requests[1]['data'];
		$I->assertSame('4111111111111111', $this->decrypt($body['encryptedCardNumber']));
		$I->assertSame('203012', $this->decrypt($body['encryptedCardExpiration']));
		$I->assertSame('123', $this->decrypt($body['encryptedCardCvv']));
		$I->assertSame('pubCryptoKey.json', $requests[0]['urn']);
		$I->assertSame('moto.json', $requests[1]['urn']);
	}

	#[Group('moto')]
	public function rejectsMissingPublicKey(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())->willReturnJson([
			'code' => 0, 'message' => 'OK', 'key' => base64_encode((string) json_encode(['notJwk' => []])),
		]);

		$I->expectThrowable(new Exception('No public encryption key for encrypting card data'), function () use ($I, $transport): void {
			(new Client($transport))->createMotoPayment($I->createPayment(), new PaymentCard('4111111111111111', '203012', '123'));
		});
	}

	#[Group('moto')]
	#[DataProvider('missingRequiredCardFields')]
	public function rejectsMissingRequiredCardFields(IntegrationTester $I, Example $example): void
	{
		$transport = (new FakeTransport())->willReturnJson($this->publicKeyResponse());

		$I->expectThrowable(new Exception($example['message']), function () use ($I, $example, $transport): void {
			$card = new PaymentCard($example['number'], $example['expiration'], '123');
			(new Client($transport))->createMotoPayment($I->createPayment(), $card);
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
	public function permitsMissingCvv(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())
			->willReturnJson($this->publicKeyResponse())
			->willReturnJson(['code' => 0, 'message' => 'OK', 'transId' => 'MOTO-1', 'status' => 'PAID']);

		$response = (new Client($transport))->createMotoPayment($I->createPayment(), new PaymentCard('4111111111111111', '203012'));
		$I->assertSame('MOTO-1', $response->getTransId());
		$I->assertArrayNotHasKey('encryptedCardCvv', $transport->requests()[1]['data']);
	}

	/**
	 * Builds a fresh RSA key pair, keeps the private half for decryption and
	 * returns the pubCryptoKey.json body carrying the public key as a JWK.
	 *
	 * @return array<string, int|string>
	 */
	private function publicKeyResponse(): array
	{
		$this->privateKey = RSA::createKey(1024);
		$publicJwk = $this->privateKey->getPublicKey()->toString('JWK');

		return [
			'code' => 0,
			'message' => 'OK',
			'key' => base64_encode((string) json_encode(['jwk' => json_decode($publicJwk, true)])),
		];
	}

	private function decrypt(string $ciphertext): string
	{
		return $this->privateKey->decrypt(base64_decode($ciphertext, true));
	}
}
