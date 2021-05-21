<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

interface ITransport
{

	/**
	 * @param mixed[] $query
	 * @param mixed[] $options
	 */
	public function get(string $uri, array $query, array $options = []): Response;

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function post(string $uri, array $data, array $options = []): Response;

}
