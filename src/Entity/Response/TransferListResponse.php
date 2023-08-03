<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Entity\Transfer;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use GuzzleHttp\Psr7\Query;

class TransferListResponse
{
       /**
        * @var array<string, int|string>
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
	 * @return array<string, int|string>
	 */
	public function getTransferList(): array
	{
		return $this->transferList;
	}

	/**
	 * @param array<string, int|string> $transferList
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
