<?php

namespace Tests\Unit\Logging;

use Codeception\Attribute\Examples;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Logging\StdoutLogger;
use Tests\Support\UnitTester;

class StdoutLoggerCest
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
		$logger = new StdoutLogger();
		ob_start();
		$functionName = $example[0];
		$logger->$functionName('message');
		$content = ob_get_contents();
		$I->assertNotEmpty($content);
		ob_clean();
	}
}
