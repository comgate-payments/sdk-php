<?php

namespace Comgate\SDK\Entity\Codes;

class ChargeUnregulatedCardFeesCode
{
	public const FULL = 'full';
	public const PARTIAL = 'partial';

	public const SELF = [
		self::FULL,
		self::PARTIAL,
	];
}
