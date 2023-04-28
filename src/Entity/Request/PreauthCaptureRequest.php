<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\LogicalException;

class PreauthCaptureRequest implements IRequest
{
	private string $transId;

	private Money $amount;

	public function __construct(string $transId, Money $amount){
		$this->setTransId($transId)
			->setAmount($amount);
	}
	
	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'capturePreauth';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		// Required
		$output = [
			'transId' => $this->getTransId(),
			'amount' => $this->getAmount()->get(),
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
	 * @return PreauthCaptureRequest
	 */
	public function setTransId(string $transId): self
	{
		$this->transId = $transId;
		return $this;
	}

	/**
	 * @return Money
	 */
	public function getAmount(): Money
	{
		return $this->amount;
	}

	/**
	 * @param Money $amount
	 * @return PreauthCaptureRequest
	 */
	public function setAmount(Money $amount): self
	{
		$this->amount = $amount;
		return $this;
	}
}
