<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

use GuzzleHttp\Psr7\Query;
use Psr\Http\Message\ResponseInterface;

class Response
{
	/** @var mixed[] */
	protected $parsed;

	/** @var mixed */
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
	 * @return mixed|string
	 */
	public function getContent()
	{
		if ($this->content === null) {
			$body = $this->origin->getBody();
			$body->rewind();

			$this->content = $body->getContents();
		}

		return $this->content;
	}
}
