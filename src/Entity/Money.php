<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

use Comgate\SDK\Exception\LogicalException;

class Money
{

	/** @var int */
	protected $value;

	protected function __construct(int $value)
	{
		$this->value = $value;
	}

	/**
	 * @param mixed $money
	 * @return static
	 */
	public static function of($money): self
	{
		if (is_int($money)) {
			return self::ofInt($money);
		}

		if (is_float($money)) {
			return self::ofFloat($money);
		}

		if ($money instanceof static) {
			return $money;
		}

		throw new LogicalException(sprintf('Only int|float|Money is supported, %s given.', gettype($money)));
	}

	/**
	 * @return static
	 */
	public static function ofInt(int $money): self
	{
		return new static($money * 100);
	}

	/**
	 * @return static
	 */
	public static function ofFloat(float $money): self
	{
		if ($money !== round($money, 2)) {
			throw new LogicalException('The price must be a maximum of two valid decimal numbers.');
		}

		return new static((int) round($money * 100));
	}

	/**
	 * @return static
	 */
	public static function ofCents(int $money): self
	{
		return new static($money);
	}

	public function get(): int
	{
		return $this->value;
	}

	public function getReal(): float
	{
		return $this->value / 100;
	}

}
