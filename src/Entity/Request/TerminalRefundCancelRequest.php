<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class TerminalRefundCancelRequest implements IRequest
{
	/** @var string */
	private $transId;

	public function __construct(string $transId)
	{
		$this->transId = $transId;
	}

	public function getUrn(): string
	{
		return 'terminalRefund/transId/{transId}.json';
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return [
			'transId' => $this->transId,
		];
	}

	public function getTransId(): string
	{
		return $this->transId;
	}

	public function setTransId(string $transId): self
	{
		$this->transId = $transId;
		return $this;
	}
}
