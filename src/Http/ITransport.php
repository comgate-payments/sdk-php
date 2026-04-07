<?php declare(strict_types = 1);

namespace Comgate\SDK\Http;

interface ITransport
{
	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function post(string $uri, array $data, array $options = []): Response;

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function postJson(string $uri, array $data, array $options = []): Response;

	/**
	 * @param mixed[] $options
	 */
	public function get(string $uri, array $options = []): Response;

	/**
	 * @param mixed[] $options
	 */
	public function delete(string $uri, array $options = []): Response;

	/**
	 * @param mixed[] $data
	 * @param mixed[] $options
	 */
	public function putJson(string $uri, array $data, array $options = []): Response;
}
