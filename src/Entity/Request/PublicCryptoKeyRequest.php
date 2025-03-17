<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Payment;

class PublicCryptoKeyRequest implements IRequest
{
	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'pubCryptoKey';
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return [];
	}
}
