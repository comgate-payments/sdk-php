<?php declare(strict_types = 1);

namespace Comgate\SDK\Logging;

use Psr\Log\AbstractLogger;

class StdoutLogger extends AbstractLogger
{

	/**
	 * @param mixed $level
	 * @param string $message
	 * @param mixed[] $context
	 */
	public function log($level, $message, array $context = []): void
	{
		echo $message;
	}

}
