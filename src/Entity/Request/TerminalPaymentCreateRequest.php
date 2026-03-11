<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\TerminalPayment;

class TerminalPaymentCreateRequest implements IRequest
{
	/** @var TerminalPayment */
	private $terminalPayment;

	public function __construct(TerminalPayment $terminalPayment)
	{
		$this->terminalPayment = $terminalPayment;
	}

	public function getUrn(): string
	{
		return 'terminalPayment.json';
	}

	/**
	 * @return array<string, int|string|null>
	 */
	public function toArray(): array
	{
		return array_filter([
			'price' => $this->terminalPayment->getPrice()->get(),
			'curr' => $this->terminalPayment->getCurr(),
			'refId' => $this->terminalPayment->getRefId(),
		], function ($v) { return $v !== null; });
	}
}
