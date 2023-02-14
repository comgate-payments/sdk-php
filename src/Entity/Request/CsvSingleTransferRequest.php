<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class CsvSingleTransferRequest implements IRequest
{

	protected string $transferId;

	protected bool $test;

	public function __construct(string $transferId, bool $test)
	{
		$this->setTransferId($transferId)
			->setTest($test);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'csvSingleTransfer';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'transferId' => $this->getTransferId(),
			'download' => 'false',
			'test' => $this->isTest() ? 'true' : 'false',
		];
	}

	/**
	 * @return string
	 */
	public function getTransferId(): string
	{
		return $this->transferId;
	}

	/**
	 * @param string $transferId
	 * @return CsvSingleTransferRequest
	 */
	public function setTransferId(string $transferId): CsvSingleTransferRequest
	{
		$this->transferId = $transferId;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isTest(): bool
	{
		return $this->test;
	}

	/**
	 * @param bool $test
	 * @return CsvSingleTransferRequest
	 */
	public function setTest(bool $test): CsvSingleTransferRequest
	{
		$this->test = $test;
		return $this;
	}
}
