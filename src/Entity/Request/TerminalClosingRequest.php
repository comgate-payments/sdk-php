<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class TerminalClosingRequest implements IRequest
{
	public function getUrn(): string
	{
		return 'terminalClosing.json';
	}

	/**
	 * @return array<string, bool|int|string|null>
	 */
	public function toArray(): array
	{
		return [];
	}
}
