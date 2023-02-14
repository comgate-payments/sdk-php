<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class SingleTransferRequest implements IRequest
{
	protected int $transferId;

	protected bool $test;

	public function __construct(int $transferId, bool $test = false)
	{
		$this->setTransferId($transferId)
			->setTest($test);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'singleTransfer';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'transferId' => $this->getTransferId(),
			'test' => $this->isTest() ? 'true' : 'false',
		];
	}

	/**
	 * @return int
	 */
	public function getTransferId(): int
	{
		return $this->transferId;
	}

	/**
	 * @param int $transferId
	 * @return SingleTransferRequest
	 */
	public function setTransferId(int $transferId): self
	{
		$this->transferId = $transferId;
		return $this;
	}

	public function isTest(): bool
	{
		return $this->test;
	}

	/**
	 * @param bool $test
	 * @return SingleTransferRequest
	 */
	public function setTest(bool $test): self
	{
		$this->test = $test;
		return $this;
	}
}
