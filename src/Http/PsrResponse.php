<?php

namespace Comgate\SDK\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class PsrResponse implements MessageInterface
{
	private $headers;
	private $body;
	private $protocolVersion = "1.1";

	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function hasHeader(string $name): bool
	{
		return isset($this->headers[$name]);
	}

	public function getHeader(string $name): array
	{
		return $this->headers[$name] ?? [];
	}

	public function getHeaderLine(string $name): string
	{
		return join(", ", $this->getHeader($name));
	}

	public function withHeader(string $name, $value): MessageInterface
	{
		$out = clone $this;
		$out->headers[$name] = is_array($value) ? $value : [$value];

		return $out;
	}

	public function withAddedHeader(string $name, $value): MessageInterface
	{
		$out = clone $this;

		if (!$out->hasHeader($name)) {
			$out->headers[$name] = [];
		}
		$out->headers[$name][] = $value;

		return $out;
	}

	public function withoutHeader(string $name): MessageInterface
	{
		$out = clone $this;
		unset($out->headers[$name]);

		return $out;
	}

	public function getBody(): StreamInterface
	{
		return $this->body;
	}

	public function withBody(StreamInterface $body): MessageInterface
	{
		$out = clone $this;
		$out->body = $body;

		return $out;
	}

	public function getProtocolVersion(): string
	{
		return $this->protocolVersion;
	}

	public function withProtocolVersion(string $version): MessageInterface
	{
		$out = clone $this;
		$out->protocolVersion = $version;

		return $out;
	}
}
