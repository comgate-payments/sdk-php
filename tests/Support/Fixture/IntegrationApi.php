<?php declare(strict_types = 1);

namespace Tests\Support\Fixture;

use Comgate\SDK\Client;
use Comgate\SDK\ClientTerminal;
use Comgate\SDK\Comgate;
use Codeception\Coverage\PhpCodeCoverageFactory;
use Psr\Log\LoggerInterface;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PrivateKey;
use RuntimeException;

final class IntegrationApi
{
	/** @var resource|null */
	private $process;

	/** @var array<int, resource> */
	private $pipes = [];

	/** @var string */
	private $url;

	/** @var string */
	private $requestLog;

	/** @var string */
	private $coverageFile;

	/** @var PrivateKey */
	private $privateKey;

	public function start(): void
	{
		$socket = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);
		if ($socket === false) {
			throw new RuntimeException($errorMessage, $errorCode);
		}

		$address = stream_socket_get_name($socket, false);
		fclose($socket);
		$port = (int) substr((string) $address, strrpos((string) $address, ':') + 1);

		$this->requestLog = tempnam(sys_get_temp_dir(), 'comgate-sdk-requests-');
		if ($this->requestLog === false) {
			throw new RuntimeException('Unable to create fixture request log');
		}
		$this->coverageFile = tempnam(sys_get_temp_dir(), 'comgate-sdk-server-coverage-');
		if ($this->coverageFile === false) {
			throw new RuntimeException('Unable to create fixture coverage file');
		}

		$router = __DIR__ . '/router.php';
		$command = [PHP_BINARY, '-d', 'xdebug.mode=coverage', '-S', '127.0.0.1:' . $port, $router];
		$this->privateKey = RSA::createKey(1024);
		$publicJwk = $this->privateKey->getPublicKey()->toString('JWK');
		$environment = array_merge($_ENV, [
			'COMGATE_FIXTURE_REQUEST_LOG' => $this->requestLog,
			'COMGATE_FIXTURE_PUBLIC_JWK' => base64_encode($publicJwk),
			'COMGATE_FIXTURE_COVERAGE_FILE' => $this->coverageFile,
			'XDEBUG_MODE' => 'coverage',
		]);
		$this->process = proc_open($command, [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ['pipe', 'w'],
		], $this->pipes, dirname($router), $environment);

		if (!is_resource($this->process)) {
			throw new RuntimeException('Unable to start fixture API');
		}

		$this->url = 'http://127.0.0.1:' . $port . '/';
		for ($attempt = 0; $attempt < 50; $attempt++) {
			$connection = @fsockopen('127.0.0.1', $port);
			if (is_resource($connection)) {
				fclose($connection);
				return;
			}
			usleep(20000);
		}

		$this->stop();
		throw new RuntimeException('Fixture API did not start');
	}

	public function stop(): void
	{
		foreach ($this->pipes as $pipe) {
			if (is_resource($pipe)) {
				fclose($pipe);
			}
		}
		$this->pipes = [];

		if (is_resource($this->process)) {
			proc_terminate($this->process);
			proc_close($this->process);
		}
		$this->process = null;

		if (isset($this->requestLog) && is_file($this->requestLog)) {
			unlink($this->requestLog);
		}
		if (isset($this->coverageFile) && is_file($this->coverageFile)) {
			unlink($this->coverageFile);
		}
	}

	public function client(string $mode = 'success', ?LoggerInterface $logger = null): Client
	{
		return $this->comgate($mode, $logger)->createClient();
	}

	public function terminalClient(string $mode = 'success', ?LoggerInterface $logger = null): ClientTerminal
	{
		return $this->comgate($mode, $logger)->createTerminalClient();
	}

	public function url(string $mode = 'success'): string
	{
		return $this->url . $mode;
	}

	/** @return array<int, array<string, mixed>> */
	public function requests(): array
	{
		$requests = [];
		foreach (file($this->requestLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
			$request = json_decode($line, true);
			if (is_array($request)) {
				$requests[] = $request;
			}
		}
		return $requests;
	}

	public function reset(): void
	{
		file_put_contents($this->requestLog, '');
	}

	public function mergeServerCoverage(): void
	{
		$coverage = unserialize(file_get_contents($this->coverageFile));
		if (!$coverage instanceof \SebastianBergmann\CodeCoverage\CodeCoverage) {
			throw new RuntimeException('Fixture server did not produce code coverage');
		}
		if (getenv('XDEBUG_MODE') === 'coverage') {
			PhpCodeCoverageFactory::build()->merge($coverage);
		}
	}

	public function decrypt(string $ciphertext): string
	{
		return $this->privateKey->decrypt(base64_decode($ciphertext, true));
	}

	private function comgate(string $mode, ?LoggerInterface $logger): Comgate
	{
		$comgate = Comgate::defaults()
			->setMerchant('fixture-merchant')
			->setSecret('fixture-secret')
			->setUrl($this->url . $mode);
		if ($logger !== null) {
			$comgate->setLogger($logger);
		}
		return $comgate;
	}
}
