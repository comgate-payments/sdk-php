<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use Comgate\SDK\Comgate;
use Comgate\SDK\Exception\Runtime\ComgateException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Transport implements ITransport
{

	/** @var ClientInterface */
	protected $client;

	/** @var Comgate */
	protected $comgate;

	public function __construct(ClientInterface $client, Comgate $comgate)
	{
		$this->client = $client;
		$this->comgate = $comgate;
	}

	/**
	 * @param mixed[] $query
	 * @param mixed[] $options
	 */
	public function get(string $uri, array $query, array $options = []): Response
	{
		$query = array_merge([
			'merchant' => $this->comgate->getMerchant(),
			'secret' => $this->comgate->getSecret(),
			'test' => $this->comgate->isTest() ? 'true' : 'false',
		], $query);

		$options = array_merge($options, [
			'query' => $query,
		]);

		try {
			$res = $this->client->request('GET', $uri, $options);
			return new Response($res);
		} catch (GuzzleException $e) {
			throw new ComgateException('Request failed', 0, $e);
		}
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function post(string $uri, array $data, array $options = []): Response
	{
		$data = array_merge([
			'merchant' => $this->comgate->getMerchant(),
			'secret' => $this->comgate->getSecret(),
			'test' => $this->comgate->isTest() ? 'true' : 'false',
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
