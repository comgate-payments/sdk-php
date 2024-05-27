<?php declare(strict_types = 1);

namespace Comgate\SDK\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class StdoutLogger implements LoggerInterface
{

	/**
	 * @param $level
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function log($level, $message, array $context = []): void
	{
		echo "[{$level}]: {$message}";
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function emergency($message, array $context = []): void
	{
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function alert($message, array $context = []): void
	{
		$this->log(LogLevel::ALERT, $message, $context);
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function critical($message, array $context = []): void
	{
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function error($message, array $context = []): void
	{
		$this->log(LogLevel::ERROR, $message, $context);
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function warning($message, array $context = []): void
	{
		$this->log(LogLevel::WARNING, $message, $context);
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function notice($message, array $context = []): void
	{
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function info($message, array $context = []): void
	{
		$this->log(LogLevel::INFO, $message, $context);
	}

	/**
	 * @param $message
	 * @param mixed[] $context
	 * @return void
	 */
	public function debug($message, array $context = []): void
	{
		$this->log(LogLevel::DEBUG, $message, $context);
	}
}
