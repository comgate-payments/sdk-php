<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

abstract class Entity
{

	/**
	 * @return mixed[]
	 */
	abstract public function toArray(): array;

}
