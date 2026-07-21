<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Codeception\Coverage\PhpCodeCoverageFactory;
use Comgate\SDK\Entity\Request\AboSingleTransferRequest;
use Comgate\SDK\Exception\ApiException;
use DateTimeImmutable;
use Tests\Support\Fixture\IntegrationApi;
use Tests\Support\IntegrationTester;

final class FixtureTransferCest
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

	#[Group('transfer')]
	#[Group('fixture')]
	public function hydratesTransferCollections(IntegrationTester $I): void
	{
		$client = $this->api->client();
		$transfers = $client->transferList(new DateTimeImmutable('2026-07-21'), true)->getTransferList();
		$I->assertCount(2, $transfers);
		$I->assertSame(1, $transfers[0]->getTransferId());
		$I->assertSame('2026-07-20', $transfers[0]->getTransferDate()->format('Y-m-d'));
		$I->assertSame('43', $transfers[1]->getVariableSymbol());

		$payments = $client->singleTransfer(42, true)->getPaymentsList();
		$I->assertCount(2, $payments);
		$I->assertSame('PAY-1', $payments[0]->getData()['transId']);

		$requests = $this->api->requests();
		$I->assertSame('/transferList/date/2026-07-21.json', $requests[0]['path']);
		$I->assertSame(['test' => 'true'], $requests[0]['query']);
		$I->assertSame('/singleTransfer/transferId/42.json', $requests[1]['path']);
	}

	#[Group('transfer')]
	#[Group('fixture')]
	public function savesCsvTransferToTemporaryFile(IntegrationTester $I): void
	{
		$response = $this->api->client()->csvSingleTransfer('42', true);
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
	#[Group('fixture')]
	#[DataProvider('aboFormats')]
	public function handlesAboTransferFormats(IntegrationTester $I, Example $example): void
	{
		$response = $this->api->client()->aboSingleTransfer('42', true, $example['type'], $example['encoding']);
		$I->assertSame('transfer.abo', $response->getFilename());
		$I->assertSame('UHL1 0100 100', $response->getFileContent());
		$request = $this->api->requests()[0];
		$I->assertSame($example['type'], $request['query']['type']);
		$I->assertSame($example['encoding'], $request['query']['encoding']);
	}

	protected function aboFormats(): array
	{
		return [
			'v1 utf8' => ['type' => AboSingleTransferRequest::ABO_TYPE_V1, 'encoding' => AboSingleTransferRequest::ABO_ENCODING_UTF8],
			'v2 windows' => ['type' => AboSingleTransferRequest::ABO_TYPE_V2, 'encoding' => AboSingleTransferRequest::ABO_ENCODING_WINDOWS],
		];
	}

	#[Group('transfer')]
	#[Group('fixture')]
	public function mapsTransferFileApiErrors(IntegrationTester $I): void
	{
		$I->expectThrowable(ApiException::class, function (): void {
			$this->api->client('api-error')->csvSingleTransfer('42', false);
		});
	}

	#[Group('transfer')]
	#[Group('fixture')]
	public function requestsAppleAssociationWithFilters(IntegrationTester $I): void
	{
		$response = $this->api->client()->getAppleDomainAssociation('APPLE_PAY', 'CZK');
		$I->assertSame('Comgate\SDK\Entity\Response\AppleDomainAssociationResponse', get_class($response));
		$request = $this->api->requests()[0];
		$I->assertSame('CZK', $request['query']['currency']);
		$I->assertArrayNotHasKey('method', $request['query']);
	}

	#[Group('transfer')]
	#[Group('fixture')]
	#[DataProvider('downloads')]
	public function streamsDownloadsInSubprocess(IntegrationTester $I, Example $example): void
	{
		$autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
		$source = dirname(__DIR__, 2) . '/src';
		$sourceFiles = [];
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source)) as $file) {
			if ($file->isFile() && $file->getExtension() === 'php') {
				$sourceFiles[] = $file->getPathname();
			}
		}
		$coverageFile = tempnam(sys_get_temp_dir(), 'comgate-download-coverage-');
		$I->assertNotFalse($coverageFile);
		$code = 'require ' . var_export($autoload, true) . '; '
			. '$filter=new \SebastianBergmann\CodeCoverage\Filter(); '
			. '$filter->includeFiles(' . var_export($sourceFiles, true) . '); '
			. '$driver=(new \SebastianBergmann\CodeCoverage\Driver\Selector())->forLineCoverage($filter); '
			. '$coverage=new \SebastianBergmann\CodeCoverage\CodeCoverage($driver,$filter); '
			. '$coverage->start("' . $example['method'] . '"); '
			. 'register_shutdown_function(function()use($coverage){$coverage->stop();file_put_contents('
			. var_export($coverageFile, true) . ',serialize($coverage));}); '
			. '$client=\Comgate\SDK\Comgate::defaults()->setMerchant("fixture-merchant")->setSecret("fixture-secret")->setUrl(' . var_export($this->api->url(), true) . ')->createClient(); '
			. '$client->' . $example['method'] . '(' . $example['arguments'] . ');';
		$process = proc_open(
			[PHP_BINARY, '-d', 'xdebug.mode=coverage', '-r', $code],
			[1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
			$pipes,
			null,
			array_merge($_ENV, ['XDEBUG_MODE' => 'coverage'])
		);
		$I->assertIsResource($process);
		$output = stream_get_contents($pipes[1]);
		$error = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		$I->assertSame(0, proc_close($process), $error);
		$I->assertStringContainsString($example['content'], $output);
		$I->assertSame($example['path'], $this->api->requests()[0]['path']);
		$downloadCoverage = unserialize(file_get_contents($coverageFile));
		$I->assertInstanceOf(\SebastianBergmann\CodeCoverage\CodeCoverage::class, $downloadCoverage);
		if (getenv('XDEBUG_MODE') === 'coverage') {
			PhpCodeCoverageFactory::build()->merge($downloadCoverage);
		}
		unlink($coverageFile);
	}

	protected function downloads(): array
	{
		return [
			'CSV' => ['method' => 'getCsvDownload', 'arguments' => '"2026-07-21", true', 'content' => 'daily.csv', 'path' => '/csvDownload/date/2026-07-21'],
			'ABO' => ['method' => 'getAboDownload', 'arguments' => '"2026-07-21", "v2", true, "utf8"', 'content' => 'daily.abo', 'path' => '/aboDownload/date/2026-07-21'],
		];
	}
}
