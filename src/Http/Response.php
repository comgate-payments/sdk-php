<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use GuzzleHttp\Psr7\Query;
use Psr\Http\Message\ResponseInterface;

class Response
{

	/** @var ResponseInterface */
	protected $origin;

	/** @var mixed[] */
	protected $parsed;

	public function __construct(ResponseInterface $origin)
	{
		$this->origin = $origin;
	}

	public function getOrigin(): ResponseInterface
	{
		return $this->origin;
	}

	public function getStatusCode(): int
	{
		return $this->origin->getStatusCode();
	}

	public function isOk(): bool
	{
		return $this->getCode() === 0;
	}

	public function getCode(): int
	{
		return (int) ($this->getField('code') ?? -1);
	}

	public function getMessage(): string
	{
		return $this->getField('message') ?? 'N/A';
	}

	/**
	 * @return mixed[]
	 */
	public function getData(): array
	{
		return $this->getParsedBody();
	}

	public function getField(string $key): mixed
	{
		return $this->getParsedBody()[$key] ?? null;
	}

	/**
	 * @return mixed[]
	 */
	protected function getParsedBody(): array
	{
		if ($this->parsed === null) {
			$body = $this->origin->getBody();
			$body->rewind();

			$content = $body->getContents();

			$this->parsed = Query::parse($content);
		}

		return $this->parsed;
	}

}
