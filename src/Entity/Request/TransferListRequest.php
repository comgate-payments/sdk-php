<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use DateTimeInterface;

class TransferListRequest implements IRequest
{
	const DATE_FORMAT = 'Y-m-d'; 
	
	/** @var string */
	protected DateTimeInterface $date;
	
	protected bool $test;

	public function __construct(DateTimeInterface $date, bool $test = false)
	{
		$this->setDate($date)
			->setTest($test);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'transferList';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'date' => $this->getDate()->format(self::DATE_FORMAT),
			'test' => $this->isTest() ? 'true' : 'false',
		];
	}

	/**
	 * @return DateTimeInterface
	 */
	public function getDate(): DateTimeInterface
	{
		return $this->date;
	}

	/**
	 * @param DateTimeInterface $date
	 * @return TransferListRequest
	 */
	public function setDate(DateTimeInterface $date): TransferListRequest
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
	 * @return TransferListRequest
	 */
	public function setTest(bool $test): TransferListRequest
	{
		$this->test = $test;
		return $this;
	}
}
