<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use Comgate\SDK\Config;
use Comgate\SDK\Exception\Runtime\ComgateException;
use Psr\Http\Message\MessageInterface;

class Transport implements ITransport
{

	/** @var Config */
	protected $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
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
		$e = curl_error($curl);

		curl_close($curl);

		if ($e) {
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
	 * @return Transport
	 */
	public function setConfig(Config $config): Transport
	{
		$this->config = $config;
		return $this;
	}

	private function createResponse($curlResponse): MessageInterface
	{
		$response = new PsrResponse();

		if (!str_contains($curlResponse, "\r\n\r\n")) {
			$curlResponse = "\r\n\r\n" . $curlResponse;
		}

		$headerSplit = explode("\r\n\r\n", $curlResponse, 2);
		$headers = $headerSplit[0];
		$body = $headerSplit[1];

		foreach (explode("\r\n", $headers) as $h) {
			if (str_contains($h, ": ")) {
				[$name, $value] = explode(": ", $h, 2);
				$response = $response->withAddedHeader($name, $value);
			}
		}

		$response = $response->withBody(new PsrStream($body));

		return $response;
	}
}
