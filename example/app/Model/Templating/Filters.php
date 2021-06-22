<?php declare(strict_types = 1);

namespace App\Model\Templating;

use Tracy\Dumper;

class Filters
{

	public static function dump(mixed $value): void
	{
		Dumper::dump($value, [
			Dumper::COLLAPSE => false,
		]);
	}

}
