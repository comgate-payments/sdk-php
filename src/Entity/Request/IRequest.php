<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

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
	 * @return mixed[]
	 */
	public function toArray(): array;

}
