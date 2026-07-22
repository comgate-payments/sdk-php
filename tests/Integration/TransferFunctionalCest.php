<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Client;
use Comgate\SDK\Entity\Request\AboSingleTransferRequest;
use Comgate\SDK\Exception\ApiException;
use DateTimeImmutable;
use Tests\Support\FakeTransport;
use Tests\Support\IntegrationTester;

/**
 * Drives the transfer endpoints of {@see Client} against a {@see FakeTransport}.
 */
final class TransferFunctionalCest
{
	#[Group('transfer')]
	public function hydratesTransferCollections(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())
			->willReturnJson([
				['transferId' => 1, 'transferDate' => '2026-07-20', 'accountCounterparty' => '0/0000', 'accountOutgoing' => '1/0000', 'variableSymbol' => '42'],
				['transferId' => 2, 'transferDate' => '2026-07-21', 'accountCounterparty' => '2/0100', 'accountOutgoing' => '3/0300', 'variableSymbol' => '43'],
			])
			->willReturnJson([['transId' => 'PAY-1', 'price' => 100], ['transId' => 'PAY-2', 'price' => 200]]);
		$client = new Client($transport);

		$transfers = $client->transferList(new DateTimeImmutable('2026-07-21'), true)->getTransferList();
		$I->assertCount(2, $transfers);
		$I->assertSame(1, $transfers[0]->getTransferId());
		$I->assertSame('2026-07-20', $transfers[0]->getTransferDate()->format('Y-m-d'));
		$I->assertSame('43', $transfers[1]->getVariableSymbol());

		$payments = $client->singleTransfer(42, true)->getPaymentsList();
		$I->assertCount(2, $payments);
		$I->assertSame('PAY-1', $payments[0]->getData()['transId']);

		$requests = $transport->requests();
		$I->assertSame('transferList/date/2026-07-21.json?test=true', $requests[0]['urn']);
		$I->assertSame('singleTransfer/transferId/42.json?test=true', $requests[1]['urn']);
	}

	#[Group('transfer')]
	public function savesCsvTransferToTemporaryFile(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())->willReturnJson(['nazev' => 'transfer.csv', 'csv' => base64_encode("id,amount\n1,100\n")]);
		$response = (new Client($transport))->csvSingleTransfer('42', true);
		$I->assertSame('transfer.csv', $response->getFilename());
		$I->assertStringContainsString('1,100', $response->getFileContent());

		$directory = sys_get_temp_dir() . '/comgate-csv-' . uniqid('', true);
		mkdir($directory);
		$response->saveToFile($directory);
		$I->assertSame($response->getFileContent(), file_get_contents($directory . '/transfer.csv'));
		unlink($directory . '/transfer.csv');
		rmdir($directory);
	}

	#[Group('transfer')]
	#[DataProvider('aboFormats')]
	public function handlesAboTransferFormats(IntegrationTester $I, Example $example): void
	{
		$transport = (new FakeTransport())->willReturnJson(['nazev' => 'transfer.abo', 'abo' => base64_encode('UHL1 0100 100')]);
		$response = (new Client($transport))->aboSingleTransfer('42', true, $example['type'], $example['encoding']);
		$I->assertSame('transfer.abo', $response->getFilename());
		$I->assertSame('UHL1 0100 100', $response->getFileContent());

		$urn = $transport->requests()[0]['urn'];
		$I->assertStringContainsString('type=' . $example['type'], $urn);
		$I->assertStringContainsString('encoding=' . $example['encoding'], $urn);
	}

	protected function aboFormats(): array
	{
		return [
			'v1 utf8' => ['type' => AboSingleTransferRequest::ABO_TYPE_V1, 'encoding' => AboSingleTransferRequest::ABO_ENCODING_UTF8],
			'v2 windows' => ['type' => AboSingleTransferRequest::ABO_TYPE_V2, 'encoding' => AboSingleTransferRequest::ABO_ENCODING_WINDOWS],
		];
	}

	#[Group('transfer')]
	public function mapsTransferFileApiErrors(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())->willReturnJson(['code' => 1500, 'message' => 'Fixture API error']);
		$I->expectThrowable(ApiException::class, function () use ($transport): void {
			(new Client($transport))->csvSingleTransfer('42', false);
		});
	}

	#[Group('transfer')]
	public function requestsAppleAssociationWithFilters(IntegrationTester $I): void
	{
		$transport = (new FakeTransport())->willReturnJson(['fileContent' => 'apple-domain-association']);
		$response = (new Client($transport))->getAppleDomainAssociation('APPLE_PAY', 'CZK');
		$I->assertSame('Comgate\SDK\Entity\Response\AppleDomainAssociationResponse', get_class($response));

		$urn = $transport->requests()[0]['urn'];
		$I->assertStringContainsString('currency=CZK', $urn);
		$I->assertStringNotContainsString('method', $urn);
	}
}
