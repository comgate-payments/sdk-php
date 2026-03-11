<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Codes;

final class TerminalStatusCode
{

	public const ONLINE = 'ONLINE';
	public const OFFLINE = 'OFFLINE';
	public const BUSY = 'BUSY';
	public const UNKNOWN = 'UNKNOWN';

	public const SELF = [
		self::ONLINE,
		self::OFFLINE,
		self::BUSY,
		self::UNKNOWN,
	];

}

