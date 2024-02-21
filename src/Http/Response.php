<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use Comgate\SDK\Http\Query;
use Psr\Http\Message\ResponseInterface;

class Response
{
	/** @var array<string, int|string> */
	protected $parsed;

	/** @var string */
	protected $content;

	/**
	 * @var ResponseInterface
	 */
	protected $origin;

	public function __construct(ResponseInterface $origin)
	{
		$this->origin = $origin;
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		if ($this->content === null) {
			$body = $this->origin->getBody();
			$body->rewind();

			$this->content = $body->getContents();
		}

		return $this->content;
	}
}
