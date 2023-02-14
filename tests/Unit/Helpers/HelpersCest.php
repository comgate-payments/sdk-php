<?php

namespace Tests\Unit\Helpers;

use Codeception\Attribute\Group;
use Comgate\SDK\Utils\Helpers;
use Tests\Support\UnitTester;

class HelpersCest
{
	#[Group('helpers')]
	public function testRedirectHelper(UnitTester $I){
		ob_start();

		Helpers::redirect('foo');
		$headers_list = xdebug_get_headers();
		header_remove();

		ob_clean();

		$I->assertContains('Location: foo', $headers_list);
	}
}
