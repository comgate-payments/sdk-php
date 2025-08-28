<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class CsvDownloadRequest implements IRequest
{
	protected string $date;

	protected bool $test;

	public function __construct(string $date, bool $test)
	{
		$this->setDate($date)
			->setTest($test);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'csvDownload';
	}

	/**
	 * @return array<string, string|int>
	 */
	public function toArray(): array
	{
		return [
			'date' => $this->getDate(),
			'test' => $this->isTest() ? 'true' : 'false',
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


}
