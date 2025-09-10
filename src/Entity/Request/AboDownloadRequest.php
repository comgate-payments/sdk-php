<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class AboDownloadRequest implements IRequest
{
	/**
	 * @var string
	 */
	protected $date;
	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var bool
	 */
	protected $test;
	/**
	 * @var string
	 */
	protected $encoding;

	public function __construct(string $date, string $type, bool $test, string $encoding)
	{
		$this->setDate($date)
			->setType($type)
			->setTest($test)
			->setEncoding($encoding);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'aboDownload';
	}

	/**
	 * @return array<string, string|int>
	 */
	public function toArray(): array
	{
		return [
			'date' => $this->getDate(),
			'type' => $this->getType(),
			'test' => $this->isTest() ? 'true' : 'false',
			'encoding' => $this->getEncoding(),
		];
	}

	/**
	 * @return string
	 */
	public function getDate(): string
	{
		return $this->date;
	}

	/**
	 * @param string $date
	 * @return $this
	 */
	public function setDate(string $date): self
	{
		$this->date = $date;
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
	 * @return $this
	 */
	public function setTest(bool $test): self
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
	 * @return self
	 */
	public function setType(string $type): self
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
	 * @return self
	 */
	public function setEncoding(string $encoding): self
	{
		$this->encoding = $encoding;
		return $this;
	}
}
