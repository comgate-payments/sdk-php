<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Http\Response;

class TerminalStatusResponse
{
	/** @var string */
	protected $status = '';

	/**
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		$data = json_decode($response->getContent(), true);

		$this->setStatus($data['status'] ?? 'UNKNOWN');
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return [
			'status' => $this->getStatus(),
		];
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 * @return self
	 */
	public function setStatus(string $status): self
	{
		$this->status = $status;
		return $this;
	}
}
