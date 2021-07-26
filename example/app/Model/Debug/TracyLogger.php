<?php declare(strict_types = 1);

namespace App\Model\Debug;

use Tracy\Debugger;
use Tracy\ILogger;
use Tracy\Logger;

class TracyLogger implements ILogger
{

	private Logger $inner;

	public function __construct()
	{
		$this->inner = new Logger(Debugger::$logDirectory, Debugger::$email, Debugger::getBlueScreen());
		$this->inner->directory = Debugger::$logDirectory;
		$this->inner->email = Debugger::$email;
	}

	/**
	 * @param mixed $value
	 * @param mixed $level
	 */
	public function log($value, $level = self::INFO): void
	{
		$this->inner->log($value, $level);
	}

}
