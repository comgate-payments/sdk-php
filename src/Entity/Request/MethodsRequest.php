<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class MethodsRequest implements IRequest
{

	public function getUrn(): string
	{
		return 'methods';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'type' => 'json',
		];
	}

}
