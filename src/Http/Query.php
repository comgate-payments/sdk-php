<?php

namespace Comgate\SDK\Http;

class Query
{
	public static function parse($string): array
	{
		$result = [];

		foreach (explode("&", $string) as $kv) {
			$exploded = explode("=", rawurldecode($kv ?? ""), 2);
			$key = $exploded[0];
			$value = $exploded[1] ?? "";
			if (array_key_exists($key, $result)) {
				if (!is_array($result[$key])) {
					$result[$key] = [$result[$key]];
				}
				$result[$key][] = $value;
			} else {
				$result[$key] = $value;
			}
		}

		return $result;
	}
}
