<?php declare(strict_types = 1);

namespace Tests\Integration;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Config;
use Comgate\SDK\Exception\Runtime\ComgateException;
use Comgate\SDK\Http\Transport;
use Psr\Log\LogLevel;
use Tests\Support\Fixture\IntegrationApi;
use Tests\Support\Fixture\RecordingLogger;
use Tests\Support\IntegrationTester;
use Throwable;

final class FixtureTransportCest
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

	#[Group('transport')]
	#[Group('fixture')]
	public function preservesResponseHeadersAndBody(IntegrationTester $I): void
	{
		$transport = new Transport(new Config('fixture-merchant', 'fixture-secret', $this->api->url()));
		$response = $transport->get('terminal.json');
		$I->assertSame('{"status":"ONLINE"}', $response->getContent());
		$I->assertSame('{"status":"ONLINE"}', $response->getContent());
		$I->assertSame(['Comgate SDK'], $response->getHeader()['X-Fixture']);
		$I->assertSame(['first', 'second'], $response->getHeader()['X-Repeated']);
	}

	#[Group('transport')]
	#[Group('fixture')]
	#[DataProvider('httpStatuses')]
	public function classifiesHttpStatusInLogger(IntegrationTester $I, Example $example): void
	{
		$logger = new RecordingLogger();
		$response = $this->api->terminalClient($example['mode'], $logger)->getTerminalStatus();
		$I->assertSame('ONLINE', $response->getStatus());
		$records = $logger->records();
		$I->assertSame($example['level'], $records[3]['level']);
		$I->assertStringContainsString($example['message'], $records[3]['message']);
	}

	protected function httpStatuses(): array
	{
		return [
			'HTTP 400' => ['mode' => 'http-400', 'level' => LogLevel::ERROR, 'message' => 'Client error'],
			'HTTP 500' => ['mode' => 'http-500', 'level' => LogLevel::CRITICAL, 'message' => 'Server error'],
		];
	}

	#[Group('transport')]
	#[Group('fixture')]
	public function reportsConnectionFailureAndLogsIt(IntegrationTester $I): void
	{
		$socket = stream_socket_server('tcp://127.0.0.1:0');
		$I->assertIsResource($socket);
		$address = stream_socket_get_name($socket, false);
		fclose($socket);
		$logger = new RecordingLogger();
		$transport = new Transport(new Config('fixture-merchant', 'fixture-secret', 'http://' . $address), $logger);

		$I->expectThrowable(ComgateException::class, function () use ($transport): void {
			$transport->get('unavailable.json');
		});
		$records = $logger->records();
		$I->assertSame(LogLevel::ERROR, $records[3]['level']);
		$I->assertStringContainsString('cURL request failed', $records[3]['message']);
	}

	#[Group('transport')]
	#[Group('fixture')]
	public function exposesMalformedJsonAsFailure(IntegrationTester $I): void
	{
		$I->expectThrowable(Throwable::class, function (): void {
			$this->api->client('malformed')->getStatus('PAY-123');
		});
	}

	#[Group('helpers')]
	#[Group('fixture')]
	public function redirectHelperReturnsHttpRedirect(IntegrationTester $I): void
	{
		$curl = curl_init($this->api->url('helper-redirect') . '?target=' . urlencode('https://example.test/paid'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$I->assertIsString($response);
		$I->assertSame(302, $status);
		$I->assertStringContainsString("Location: https://example.test/paid\r\n", $response);
		$I->assertStringEndsWith('redirected', $response);
		$this->api->mergeServerCoverage();
	}

	#[Group('transport')]
	#[Group('fixture')]
	public function parsesEncodedHttpQuery(IntegrationTester $I): void
	{
		$query = 'message=payment%20accepted%3Dtrue&empty&currency=CZK&reference=order%26item';
		$curl = curl_init($this->api->url('query-parse') . '?' . $query);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$I->assertSame(200, $status);
		$I->assertIsString($response);
		$I->assertSame([
			'message' => 'payment accepted=true',
			'empty' => '',
			'currency' => 'CZK',
			'reference' => 'order&item',
		], json_decode($response, true));
		$this->api->mergeServerCoverage();
	}
}
