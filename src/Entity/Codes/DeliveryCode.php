<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Codes;

final class DeliveryCode
{

	public const HOME_DELIVERY = 'HOME_DELIVERY';
	public const PICKUP = 'PICKUP';
	public const ELECTRONIC_DELIVERY = 'ELECTRONIC_DELIVERY';

	public const SELF = [
		self::HOME_DELIVERY,
		self::PICKUP,
		self::ELECTRONIC_DELIVERY,
	];

}
