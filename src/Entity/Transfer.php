<?php

namespace Comgate\SDK\Entity;

use DateTime;
use DateTimeInterface;

class Transfer
{
	protected int $transferId; // int(1444754)
	protected DateTimeInterface $transferDate; // string(10) "2023-02-07"
	protected string $accountCounterparty; // string(6) "0/0000"
	protected string $accountOutgoing; // string(6) "1/0000"
	protected string $variableSymbol; // string(10) "0123456789"
        
        /**
         * 
         * @param array<string> $transferData
         * @return Transfer
         */
	public function fromArray(array $transferData): Transfer
        {
		$this->setTransferId((int) $transferData['transferId'])
			->setTransferDate(new DateTime($transferData['transferDate']))
			->setAccountCounterparty($transferData['accountCounterparty'])
			->setAccountOutgoing($transferData['accountOutgoing'])
			->setVariableSymbol($transferData['variableSymbol']);

		return $this;
	}

	/**
	 * @return int
	 */
	public function getTransferId(): int
	{
		return $this->transferId;
	}

	/**
	 * @param int $transferId
	 * @return Transfer
	 */
	public function setTransferId(int $transferId): Transfer
	{
		$this->transferId = $transferId;
		return $this;
	}

	/**
	 * @return DateTimeInterface
	 */
	public function getTransferDate(): DateTimeInterface
	{
		return $this->transferDate;
	}

	/**
	 * @param DateTimeInterface $transferDate
	 * @return Transfer
	 */
	public function setTransferDate(DateTimeInterface $transferDate): Transfer
	{
		$this->transferDate = $transferDate;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAccountCounterparty(): string
	{
		return $this->accountCounterparty;
	}

	/**
	 * @param string $accountCounterparty
	 * @return Transfer
	 */
	public function setAccountCounterparty(string $accountCounterparty): Transfer
	{
		$this->accountCounterparty = $accountCounterparty;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAccountOutgoing(): string
	{
		return $this->accountOutgoing;
	}

	/**
	 * @param string $accountOutgoing
	 * @return Transfer
	 */
	public function setAccountOutgoing(string $accountOutgoing): Transfer
	{
		$this->accountOutgoing = $accountOutgoing;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVariableSymbol(): string
	{
		return $this->variableSymbol;
	}

	/**
	 * @param string $variableSymbol
	 * @return Transfer
	 */
	public function setVariableSymbol(string $variableSymbol): Transfer
	{
		$this->variableSymbol = $variableSymbol;
		return $this;
	}


}
