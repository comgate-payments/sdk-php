<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Entity\PaymentInfo;
use Comgate\SDK\Entity\Transfer;
use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Comgate\SDK\Http\Query;

class SingleTransferResponse
{
	/**
	 * @var array<int, PaymentInfo>
	 */
	protected array $paymentsList = [];

	/**
	 * @param Response $singleTransferResponse
	 */
	public function __construct(Response $singleTransferResponse)
	{
		$paymentsInfoList = json_decode($singleTransferResponse->getContent(), true);


		foreach ($paymentsInfoList as $paymentData) {
			$paymentInfo = (new PaymentInfo())->fromArray($paymentData);
			$this->addPaymentInfo($paymentInfo);
		}
	}

	/**
	 * @return array<int, PaymentInfo>
	 */
	public function getPaymentsList(): array
	{
		return $this->paymentsList;
	}

	/**
	 * @param array<int, PaymentInfo> $paymentsList
	 * @return SingleTransferResponse
	 */
	public function setPaymentsList(array $paymentsList): self
	{
		$this->paymentsList = $paymentsList;
		return $this;
	}

	public function addPaymentInfo(PaymentInfo $paymentInfo): self
	{
		$this->paymentsList[] = $paymentInfo;
		return $this;
	}
}
