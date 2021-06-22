<?php declare(strict_types = 1);

namespace Comgate\SDK\Utils;

class Helpers
{

	public static function redirect(string $url): void
	{
		header('Location: ' . $url);
		die();
	}

}
