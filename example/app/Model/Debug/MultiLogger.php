<?php declare(strict_types = 1);

namespace App\Model\Debug;

use Throwable;
use Tracy\ILogger;

class MultiLogger implements ILogger
{

	/** @var ILogger[] */
	private array $loggers = [];

	/**
	 * @param ILogger[] $loggers
	 */
	public function __construct(array $loggers)
	{
		$this->loggers = $loggers;
	}

	/**
	 * @param mixed $value
	 * @param mixed $level
	 */
	public function log($value, $level = self::INFO): void
	{
		foreach ($this->loggers as $logger) {
			try {
				$logger->log($value, $level);
			} catch (Throwable $e) {
				// Just nothing
			}
		}
	}

}
