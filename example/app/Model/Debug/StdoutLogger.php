<?php declare(strict_types = 1);

namespace App\Model\Debug;

use Tracy\ILogger;
use Tracy\Logger;

class StdoutLogger implements ILogger
{

	/**
	 * @param mixed $value
	 * @param mixed $level
	 */
	public function log($value, $level = self::INFO): void
	{
		$message = Logger::formatLogLine($value);
		file_put_contents('php://stdout', sprintf('[%s] %s', $level, $message) . PHP_EOL);
	}

}
