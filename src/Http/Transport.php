<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use Comgate\SDK\Config;
use Comgate\SDK\Exception\Runtime\ComgateException;
use Psr\Http\Message\MessageInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Transport implements ITransport
{

	/** @var Config */
	protected $config;

	/** @var LoggerInterface | null */
	private $logger;

	public function __construct(Config $config, ?LoggerInterface $logger = null)
	{
		$this->config = $config;
		$this->logger = $logger;
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function post(string $urn, array $data, array $options = []): Response
	{
		$data = array_merge([
			'merchant' => $this->config->getMerchant(),
			'secret' => $this->config->getSecret(),
		], $data);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $this->config->getUrl() . $urn);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["content-type: application/x-www-form-urlencoded",]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$e = curl_error($curl);

		if ($this->logger !== null) {
			$this->logger->log(LogLevel::INFO, 'Request to "' . $this->config->getUrl() . $urn . '" sent');
			$this->logger->log(LogLevel::DEBUG, 'Response: ' . $response);
			$this->logger->log(LogLevel::DEBUG, 'cURL info: ' . json_encode(curl_getinfo($curl)));

			// => [level, message]
			switch (true){
				case ($response === false):
					$log = [LogLevel::ERROR, 'cURL request failed: ' . $e];
					break;
				case ($httpCode >= 500):
					$log = [LogLevel::CRITICAL, 'Server error: HTTP code ' . $httpCode];
					break;
				case ($httpCode >= 400):
					$log = [LogLevel::ERROR, 'Client error: HTTP code ' . $httpCode];
					break;
				default:
					$log = [LogLevel::INFO, 'cURL request completed successfully.'];
					break;
			}
			$this->logger->log(...$log);
		}

		curl_close($curl);

		if ($e != '') {
			throw new ComgateException("Request failed: {$e}", 0);
		}

		return new Response(self::createResponse($response));
	}

	/**
	 * @return Config
	 */
	public function getConfig(): Config
	{
		return $this->config;
	}

	/**
	 * @param Config $config
	 * @return ITransport
	 */
	public function setConfig(Config $config): ITransport
	{
		$this->config = $config;
		return $this;
	}

	/**
	 * @param bool|string $curlResponse
	 * @return MessageInterface
	 */
	private function createResponse($curlResponse): MessageInterface
	{
		$response = new PsrResponse();

		if (strstr((string)$curlResponse, "\r\n\r\n") === false) {
			$curlResponse = "\r\n\r\n" . $curlResponse;
		}

		$headerSplit = explode("\r\n\r\n", (string)$curlResponse, 2);
		$headers = $headerSplit[0];
		$body = $headerSplit[1];

		foreach (explode("\r\n", $headers) as $h) {
			if (strstr($h, ": ") !== false) {
				[$name, $value] = explode(": ", $h, 2);
				$response = $response->withAddedHeader($name, $value);
			}
		}

		$response = $response->withBody(new PsrStream($body));

		return $response;
	}
}
