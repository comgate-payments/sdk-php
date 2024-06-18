<?php

namespace Comgate\SDK\Http;

class Query
{
	/**
	 * @param string $string
	 * @return array<string, string>
	 */
	public static function parse(string $string): array
	{
		/** @var array<string, string> */
		$result = [];

		foreach (explode("&", $string) as $keyValuePair) {
			$exploded = explode("=", rawurldecode($keyValuePair), 2);
			$key = $exploded[0];
			$value = $exploded[1] ?? "";
			$result[$key] = $value;
			// $exploded = explode("=", rawurldecode($keyValuePair), 2);
			// $key = $exploded[0];
			// $value = $exploded[1] ?? "";
			// if (array_key_exists($key, $result)) {
			// 	if (!is_array($result[$key])) {
			// 		$result[$key] = [$result[$key]];
			// 	}
			// 	$result[$key][] = $value;
			// } else {
			// 	$result[$key] = $value;
			// }
		}

		return $result;
	}
}
