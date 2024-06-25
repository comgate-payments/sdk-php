<?php

namespace Comgate\SDK\Http;

use Psr\Http\Message\StreamInterface;

class PsrStream implements StreamInterface
{
	/** @var resource */
	protected $stream;

	public function __construct(string $content)
	{
		$stream = fopen('php://temp', 'r+');

		if ($stream !== false) {
			$this->stream = $stream;
			fwrite($this->stream, $content);
			rewind($this->stream);
		}
	}

	public function __toString(): string
	{
		rewind($this->stream);
		$streamString = stream_get_contents($this->stream);
		return $streamString !== false ? $streamString : '';
	}

	public function close(): void
	{
		fclose($this->stream);
	}

	public function detach()
	{
		$detached = $this->stream;
		return $detached;
	}

	public function getSize(): ?int
	{
		$stats = fstat($this->stream);
		if ($stats !== false) {
			return $stats['size'];
		}
		return null;
	}

	public function tell(): int
	{
		$stream = ftell($this->stream);
		return $stream !== false ? $stream : 0;
	}

	public function eof(): bool
	{
		return feof($this->stream);
	}

	public function isSeekable(): bool
	{
		return true;
	}

	public function seek($offset, $whence = SEEK_SET): void
	{
		fseek($this->stream, $offset, $whence);
	}

	public function rewind(): void
	{
		$this->seek(0);
	}

	public function isWritable(): bool
	{
		return true;
	}

	public function write($string): int
	{
		$stream = fwrite($this->stream, $string);
		return $stream !== false ? $stream : 0;
	}

	public function isReadable(): bool
	{
		return true;
	}

	public function read($length): string
	{
		$length = max($length, 0);

		$stream = fread($this->stream, $length);
		return $stream == false ? '' : $stream;
	}

	public function getContents(): string
	{
		$stream = stream_get_contents($this->stream);
		return $stream !== false ? $stream : '';
	}

	public function getMetadata($key = null)
	{
		return $key != false ? stream_get_meta_data($this->stream)[$key] ?? null : stream_get_meta_data($this->stream);
	}
}
