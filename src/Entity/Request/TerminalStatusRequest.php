<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

class TerminalStatusRequest implements IRequest
{
	public function getUrn(): string
	{
		return 'terminal.json';
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return [];
	}
}
