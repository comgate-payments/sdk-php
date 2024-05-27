<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\LogicalException;

class PreauthCancelRequest implements IRequest
{
	private string $transId;

	public function __construct(string $transId){
		$this->setTransId($transId);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'cancelPreauth';
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		// Required
		$output = [
			'transId' => $this->getTransId(),
		];

		return $output;
	}

	/**
	 * @return string
	 */
	public function getTransId(): string
	{
		return $this->transId;
	}

	/**
	 * @param string $transId
	 * @return PreauthCancelRequest
	 */
	public function setTransId(string $transId): self
	{
		$this->transId = $transId;
		return $this;
	}
}
