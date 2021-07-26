<?php declare(strict_types = 1);

namespace Comgate\SDK\Logging;

use Psr\Log\AbstractLogger;

class FileLogger extends AbstractLogger
{

	private string $file;

	public function __construct(string $file)
	{
		$this->file = $file;
	}

	/**
	 * @param mixed $level
	 * @param string $message
	 * @param mixed[] $context
	 */
	public function log($level, $message, array $context = []): void
	{
		file_put_contents($this->file, $message . "\n", FILE_APPEND);
	}

}
