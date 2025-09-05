<?php

namespace Tests\Unit\Helpers;

use Codeception\Attribute\Group;
use Comgate\SDK\Utils\Helpers;
use Tests\Support\UnitTester;

class HelpersCest
{
	/**
	 * Tests redirect by setting location header
	 */
	#[Group('helpers')]
	public function testRedirectHelper(UnitTester $I){
		if($_ENV['APPLICATION_ENV'] === 'sdk-github'){
			$I->markTestSkipped('Tento test byl v gitlabu přeskočen, ');
			return;
		}
		ob_start();

		Helpers::redirect('foo');
		$headers_list = xdebug_get_headers();
		header_remove();

		ob_clean();

		$I->assertContains('Location: foo', $headers_list);
	}
}
