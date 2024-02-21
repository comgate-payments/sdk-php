<?php

namespace Unit\Entity;

use Comgate\SDK\Entity\Money;
use Tests\Support\UnitTester;

class MoneyCest
{
	public function FloatingPointMultiplicationTest(UnitTester $I) {
		for ($m = 0; $m <= 10;) {
			$m = round($m + 0.01, 2);
			$I->assertEquals(Money::ofFloat($m)->get(), (int)round(($m * 100)));
		}
	}
}
