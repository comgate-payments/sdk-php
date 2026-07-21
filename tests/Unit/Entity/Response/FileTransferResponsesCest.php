<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\Group;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\AboSingleTransferResponse;
use Comgate\SDK\Entity\Response\CsvSingleTransferResponse;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

#[Group('file-transfer-response')]
class FileTransferResponsesCest
{
	// -----------------------------------------------------------------------
	// AboSingleTransferResponse
	// -----------------------------------------------------------------------

	public function aboSingleTransferSuccessDecodesBase64Test(UnitTester $I): void
	{
		$content = 'ABO file content here';
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'abo'   => base64_encode($content),
				'nazev' => 'transfer_20230201.abo',
			]),
		]);

		$response = new AboSingleTransferResponse($mock);

		$I->assertEquals('transfer_20230201.abo', $response->getFilename());
		$I->assertEquals($content, $response->getFileContent());
	}

	public function aboSingleTransferApiErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'Transfer not found']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new AboSingleTransferResponse($mock);
		});
	}

	public function aboSingleTransferApiErrorHasCorrectCodeTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1404, 'message' => 'Not found']),
		]);

		try {
			new AboSingleTransferResponse($mock);
			$I->fail('Expected ApiException to be thrown');
		} catch (ApiException $e) {
			$I->assertEquals(1404, $e->getCode());
			$I->assertEquals('Not found', $e->getMessage());
		}
	}

	public function aboSingleTransferInvalidBase64YieldsEmptyContentTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'abo'   => '!!!invalid-base64!!!',
				'nazev' => 'file.abo',
			]),
		]);

		$response = new AboSingleTransferResponse($mock);

		$I->assertEquals('', $response->getFileContent());
		$I->assertEquals('file.abo', $response->getFilename());
	}

	// -----------------------------------------------------------------------
	// CsvSingleTransferResponse
	// -----------------------------------------------------------------------

	public function csvSingleTransferSuccessDecodesBase64Test(UnitTester $I): void
	{
		$content = "col1,col2\nval1,val2";
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'csv'   => base64_encode($content),
				'nazev' => 'transfer_20230201.csv',
			]),
		]);

		$response = new CsvSingleTransferResponse($mock);

		$I->assertEquals('transfer_20230201.csv', $response->getFilename());
		$I->assertEquals($content, $response->getFileContent());
	}

	public function csvSingleTransferApiErrorThrowsTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 1500, 'message' => 'CSV not available']),
		]);

		$I->expectThrowable(ApiException::class, function () use ($mock) {
			new CsvSingleTransferResponse($mock);
		});
	}

	public function csvSingleTransferApiErrorHasCorrectCodeTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode(['code' => 9999, 'message' => 'Server error']),
		]);

		try {
			new CsvSingleTransferResponse($mock);
			$I->fail('Expected ApiException to be thrown');
		} catch (ApiException $e) {
			$I->assertEquals(9999, $e->getCode());
		}
	}

	public function csvSingleTransferInvalidBase64YieldsEmptyContentTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, [
			'getContent' => json_encode([
				'csv'   => '!!!invalid-base64!!!',
				'nazev' => 'file.csv',
			]),
		]);

		$response = new CsvSingleTransferResponse($mock);

		$I->assertEquals('', $response->getFileContent());
	}
}
