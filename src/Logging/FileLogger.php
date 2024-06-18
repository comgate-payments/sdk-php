<?php declare(strict_types = 1);

namespace Comgate\SDK\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class FileLogger implements LoggerInterface
{

	/** @var string */
	private $file;

	public function __construct(string $file)
	{
		$this->file = $file;
	}

	/**
	 * @param mixed $level
	 * @param string|Stringable $message
	 * @param mixed[] $context
	 */
	public function log($level, $message, array $context = []): void
	{
		file_put_contents($this->file, $message . "\n", FILE_APPEND);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function emergency($message, array $context = []): void
	{
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function alert($message, array $context = []): void
	{
		$this->log(LogLevel::ALERT, $message, $context);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function critical($message, array $context = []): void
	{
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function error($message, array $context = []): void
	{
		$this->log(LogLevel::ERROR, $message, $context);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function warning($message, array $context = []): void
	{
		$this->log(LogLevel::WARNING, $message, $context);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function notice($message, array $context = []): void
	{
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function info($message, array $context = []): void
	{
		$this->log(LogLevel::INFO, $message, $context);
	}

	/**
	 * @param $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function debug($message, array $context = []): void
	{
		$this->log(LogLevel::DEBUG, $message, $context);
	}
}
