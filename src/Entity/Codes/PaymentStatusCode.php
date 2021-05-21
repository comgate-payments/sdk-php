<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Codes;

final class PaymentStatusCode
{

	public const PENDING = 'PENDING';
	public const PAID = 'PAID';
	public const CANCELLED = 'CANCELLED';
	public const AUTHORIZED = 'AUTHORIZED';

	public const SELF = [
		self::PENDING,
		self::PAID,
		self::CANCELLED,
		self::AUTHORIZED,
	];

}
