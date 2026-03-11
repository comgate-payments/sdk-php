<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\TerminalRefund;

class TerminalRefundCreateRequest implements IRequest
{
	/** @var TerminalRefund */
	private $terminalRefund;

	public function __construct(TerminalRefund $terminalRefund)
	{
		$this->terminalRefund = $terminalRefund;
	}

	public function getUrn(): string
	{
		return 'terminalRefund.json';
	}

	/**
	 * @return array<string, int|string|null>
	 */
	public function toArray(): array
	{
		return array_filter([
			'price' => $this->terminalRefund->getPrice()->get(),
			'curr' => $this->terminalRefund->getCurr(),
			'refId' => $this->terminalRefund->getRefId(),
		], function ($v) { return $v !== null; });
	}
}
