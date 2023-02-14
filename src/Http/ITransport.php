<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

interface ITransport
{
	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function post(string $uri, array $data, array $options = []): Response;
}
