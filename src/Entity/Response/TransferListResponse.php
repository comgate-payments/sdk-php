<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Entity\Transfer;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Comgate\SDK\Http\Query;

class TransferListResponse
{
	/**
	 * @var array<int, Transfer>
	 */
	protected array $transferList = [];

	/**
	 * @param Response $transferListResponse
	 */
	public function __construct(Response $transferListResponse)
	{
		$transferListJson = $transferListResponse->getContent();
		$transferList = (array) json_decode($transferListResponse->getContent(), true);

		foreach ($transferList as $transferData) {
			$transfer = (new Transfer())->fromArray($transferData);
			$this->addTransfer($transfer);
		}
	}

	/**
	 * @return array<int, Transfer>
	 */
	public function getTransferList(): array
	{
		return $this->transferList;
	}

	/**
	 * @param array<int, Transfer> $transferList
	 * @return TransferListResponse
	 */
	public function setTransferList(array $transferList): self
	{
		$this->transferList = $transferList;
		return $this;
	}

	public function addTransfer(Transfer $transfer): self
	{
		$this->transferList[] = $transfer;
		return $this;
	}
}
