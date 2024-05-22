<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use Psr\Http\Message\MessageInterface;

class Response
{
	/** @var array<string, int|string> */
	protected $parsed;

	/** @var string */
	protected $content;

	/**
	 * @var MessageInterface
	 */
	protected $origin;

	public function __construct(MessageInterface $origin)
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
