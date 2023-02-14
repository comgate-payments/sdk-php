<?php

namespace Tests\Unit\Logging;

use Codeception\Attribute\Examples;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Logging\FileLogger;
use Comgate\SDK\Logging\StdoutLogger;
use Tests\Support\UnitTester;

class FileLoggerCest
{
	#[Group('log')]
	#[Examples('info')]
	#[Examples('debug')]
	#[Examples('notice')]
	#[Examples('error')]
	#[Examples('warning')]
	#[Examples('alert')]
	#[Examples('critical')]
	#[Examples('emergency')]
	public function testLogging(UnitTester $I, Example $example)
	{
		$tmpFile = tempnam(sys_get_temp_dir(), 'logger_test');
		$I->assertIsEmpty(file_get_contents($tmpFile));
		$logger = new FileLogger($tmpFile);
		$functionName = $example[0];
		$logger->$functionName('message');
		$I->assertFileExists($tmpFile);
		$I->assertNotEmpty(file_get_contents($tmpFile));
	}
}
