<?php

namespace Comgate\SDK\Entity\Codes;

class TypeCode
{
	public const TYPE_JSON = 'json';
	public const TYPE_XML = 'xml';

	public const SELF = [
		self::TYPE_JSON,
		self::TYPE_XML,
	];
}
