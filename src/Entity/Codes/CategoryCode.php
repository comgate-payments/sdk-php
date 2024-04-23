<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Codes;

final class CategoryCode
{

	public const PHYSICAL_GOODS_ONLY = 'PHYSICAL_GOODS_ONLY';
	public const OTHER = 'OTHER';

	public const SELF = [
		self::PHYSICAL_GOODS_ONLY,
		self::OTHER,
	];

}
