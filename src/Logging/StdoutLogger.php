<?php declare(strict_types = 1);

namespace Comgate\SDK\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class StdoutLogger implements LoggerInterface
{

	/**
	 * @param mixed $level
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function log($level, $message, array $context = []): void
	{
            $level = (string) $level;
            echo "[{$level}]: {$message}";
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function emergency($message, array $context = []): void
	{
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function alert($message, array $context = []): void
	{
		$this->log(LogLevel::ALERT, $message, $context);
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function critical($message, array $context = []): void
	{
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function error($message, array $context = []): void
	{
		$this->log(LogLevel::ERROR, $message, $context);
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function warning($message, array $context = []): void
	{
		$this->log(LogLevel::WARNING, $message, $context);
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function notice($message, array $context = []): void
	{
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function info($message, array $context = []): void
	{
		$this->log(LogLevel::INFO, $message, $context);
	}

	/**
	 * @param string|Stringable $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function debug($message, array $context = []): void
	{
		$this->log(LogLevel::DEBUG, $message, $context);
	}
}
