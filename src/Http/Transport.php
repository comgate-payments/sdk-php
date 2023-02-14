<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use Comgate\SDK\Config;
use Comgate\SDK\Exception\Runtime\ComgateException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Transport implements ITransport
{

	/** @var ClientInterface */
	protected $client;

	/** @var Config */
	protected $config;

	public function __construct(ClientInterface $client, Config $config)
	{
		$this->client = $client;
		$this->config = $config;
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function post(string $uri, array $data, array $options = []): Response
	{
		$data = array_merge([
			'merchant' => $this->config->getMerchant(),
			'secret' => $this->config->getSecret(),
		], $data);

		$options = array_merge($options, [
			'form_params' => $data,
		]);

		try {
			$res = $this->client->request('POST', $uri, $options);
			return new Response($res);
		} catch (GuzzleException $e) {
			throw new ComgateException('Request failed', 0, $e);
		}
	}

}
