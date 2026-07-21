<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\Group;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\AppleDomainAssociationResponse;
use Comgate\SDK\Entity\Response\PublicCryptoKeyResponse;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

#[Group('misc-response')]
class MiscResponsesCest
{
	// -----------------------------------------------------------------------
	// PublicCryptoKeyResponse
	// -----------------------------------------------------------------------

	public function publicCryptoKeySuccessHydratesKeyTest(UnitTester $I): void
	{
		$keyData = base64_encode(json_encode(['jwk' => ['kty' => 'RSA', 'n' => 'abc123']]));

		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'code'    => 0,
				'message' => 'OK',
				'key'     => $keyData,
			]),
		]);

		$response = new PublicCryptoKeyResponse($mock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('OK', $response->getMessage());
		$I->assertEquals($keyData, $response->getKey());
	}

	public function publicCryptoKeyErrorThrowsApiExceptionTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Key not available']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new PublicCryptoKeyResponse($mock);
		});
	}

	public function publicCryptoKeyToArrayContainsAllFieldsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 0, 'message' => 'OK', 'key' => 'mykey']),
		]);

		$array = (new PublicCryptoKeyResponse($mock))->toArray();

		$I->assertArrayHasKey('code', $array);
		$I->assertArrayHasKey('message', $array);
		$I->assertArrayHasKey('key', $array);
		$I->assertCount(3, $array);
	}

	// -----------------------------------------------------------------------
	// AppleDomainAssociationResponse
	// -----------------------------------------------------------------------

	public function appleDomainAssociationSuccessHydratesFileContentTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['fileContent' => 'apple-developer-merchantid-domain-association']),
		]);

		$response = new AppleDomainAssociationResponse($mock);

		// Response does not expose getFileContent() — just verify it doesn't throw
		$I->assertInstanceOf(AppleDomainAssociationResponse::class, $response);
	}

	public function appleDomainAssociationCode1400ThrowsMissingParamExceptionTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1400, 'message' => 'Missing parameter']),
		]);

		$I->expectThrowable(MissingParamException::class, function () use ($mock) {
			new AppleDomainAssociationResponse($mock);
		});
	}

	public function appleDomainAssociationDefaultErrorThrowsApiExceptionTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Apple Pay not enabled']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new AppleDomainAssociationResponse($mock);
		});
	}

	public function appleDomainAssociationErrorCodeIsPreservedTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 9876, 'message' => 'Custom error']),
		]);

		try {
			new AppleDomainAssociationResponse($mock);
			$I->fail('Expected ApiException');
		} catch (ApiException $e) {
			$I->assertEquals(9876, $e->getCode());
			$I->assertEquals('Custom error', $e->getMessage());
		}
	}
}
