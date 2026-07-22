<?php declare(strict_types = 1);

namespace Tests\Support;

use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Http\PsrResponse;
use Comgate\SDK\Http\PsrStream;
use Comgate\SDK\Http\Response;
use RuntimeException;

/**
 * In-memory {@see ITransport} used by the functional tests.
 *
 * Instead of booting a fake HTTP server, each test queues the response(s) the
 * SDK should receive and then inspects what the SDK actually sent. Responses are
 * handed back in the order they were queued - one per transport call - so a test
 * that drives a multi-step workflow simply queues one response per step.
 */
final class FakeTransport implements ITransport
{
	/** @var array<int, Response> */
	private $responses = [];

	/** @var array<int, array{method: string, urn: string, data: array<string, mixed>}> */
	private $requests = [];

	/**
	 * Queue the next response as a JSON body built from $data - the shape the
	 * Comgate API returns for a successful call.
	 *
	 * @param array<int|string, mixed> $data
	 */
	public function willReturnJson(array $data): self
	{
		return $this->willReturn((string) json_encode($data));
	}

	/**
	 * Queue the next response with a raw (possibly malformed) body.
	 */
	public function willReturn(string $body): self
	{
		$message = (new PsrResponse())
			->withHeader('Content-Type', 'application/json')
			->withBody(new PsrStream($body));
		$this->responses[] = new Response($message);

		return $this;
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function post(string $uri, array $data, array $options = []): Response
	{
		return $this->record('POST', $uri, $data);
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function postJson(string $uri, array $data, array $options = []): Response
	{
		return $this->record('POST', $uri, $data);
	}

	/**
	 * @param mixed[] $options
	 */
	public function get(string $uri, array $options = []): Response
	{
		return $this->record('GET', $uri, []);
	}

	/**
	 * @param mixed[] $options
	 */
	public function delete(string $uri, array $options = []): Response
	{
		return $this->record('DELETE', $uri, []);
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function putJson(string $uri, array $data, array $options = []): Response
	{
		return $this->record('PUT', $uri, $data);
	}

	/**
	 * Everything the SDK sent, in call order. Each entry is the HTTP method, the
	 * URN (path + query string) and the payload the SDK handed to the transport.
	 *
	 * @return array<int, array{method: string, urn: string, data: array<string, mixed>}>
	 */
	public function requests(): array
	{
		return $this->requests;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	private function record(string $method, string $urn, array $data): Response
	{
		$this->requests[] = ['method' => $method, 'urn' => $urn, 'data' => $data];

		if ($this->responses === []) {
			throw new RuntimeException(sprintf('FakeTransport has no queued response for %s %s', $method, $urn));
		}

		return array_shift($this->responses);
	}
}
