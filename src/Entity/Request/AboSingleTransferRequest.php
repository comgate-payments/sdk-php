<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class AboSingleTransferRequest implements IRequest
{
	const ABO_TYPE_V1 = 'v1';
	const ABO_TYPE_V2 = 'v2';

	const ABO_ENCODING_WINDOWS = 'win1250';
	const ABO_ENCODING_UTF8 = 'utf8';

	protected string $transferId;

	protected bool $test;

	protected string $type;

	protected string $encoding;
	private bool $download = false; // just for method sync Cest to pass, should be always false


	public function __construct(string $transferId, bool $test, string $type, string $encoding)
	{
		$this->setTransferId($transferId)
			->setTest($test)
			->setType($type)
			->setEncoding($encoding);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'aboSingleTransfer';
	}

	/**
	 * @return array<string, string|int>
	 */
	public function toArray(): array
	{
		return [
			'transferId' => $this->getTransferId(),
			'download' => 'false',
			'test' => $this->isTest() ? 'true' : 'false',
			'type' => $this->getType(),
			'encoding' => $this->getEncoding(),
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
	 * @return AboSingleTransferRequest
	 */
	public function setTransferId(string $transferId): AboSingleTransferRequest
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
	 * @return AboSingleTransferRequest
	 */
	public function setTest(bool $test): AboSingleTransferRequest
	{
		$this->test = $test;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return AboSingleTransferRequest
	 */
	public function setType(string $type): AboSingleTransferRequest
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEncoding(): string
	{
		return $this->encoding;
	}

	/**
	 * @param string $encoding
	 * @return AboSingleTransferRequest
	 */
	public function setEncoding(string $encoding): AboSingleTransferRequest
	{
		$this->encoding = $encoding;
		return $this;
	}
}
