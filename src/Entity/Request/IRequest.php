<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Money;

interface IRequest
{

	/**
	 * Returns a endpoint URN part.
	 *
	 * @return string
	 */
	public function getUrn(): string;

	/**
	 * Converts the request to array params.
	 *
	 * @return array<string, bool|string|int|null>
	 */
	public function toArray(): array;

}
