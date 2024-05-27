<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\Refund;
use Comgate\SDK\Entity\Request\AboSingleTransferRequest;
use Comgate\SDK\Entity\Request\CsvSingleTransferRequest;
use Comgate\SDK\Entity\Request\PaymentCreateRequest;
use Comgate\SDK\Entity\Request\MethodsRequest;
use Comgate\SDK\Entity\Request\PaymentCancelRequest;
use Comgate\SDK\Entity\Request\PaymentRefundRequest;
use Comgate\SDK\Entity\Request\PreauthCancelRequest;
use Comgate\SDK\Entity\Request\PaymentStatusRequest;
use Comgate\SDK\Entity\Request\PreauthCaptureRequest;
use Comgate\SDK\Entity\Request\RecurringPaymentRequest;
use Comgate\SDK\Entity\Request\SimulationRequest;
use Comgate\SDK\Entity\Request\SingleTransferRequest;
use Comgate\SDK\Entity\Request\TransferListRequest;
use Comgate\SDK\Entity\Response\AboSingleTransferResponse;
use Comgate\SDK\Entity\Response\CsvSingleTransferResponse;
use Comgate\SDK\Entity\Response\MethodsResponse;
use Comgate\SDK\Entity\Response\PaymentCancelResponse;
use Comgate\SDK\Entity\Response\PaymentCreateResponse;
use Comgate\SDK\Entity\Response\PaymentStatusResponse;
use Comgate\SDK\Entity\Response\PreauthCancelResponse;
use Comgate\SDK\Entity\Response\PreauthCaptureResponse;
use Comgate\SDK\Entity\Response\RecurringPaymentResponse;
use Comgate\SDK\Entity\Response\RefundResponse;
use Comgate\SDK\Entity\Response\SimulationResponse;
use Comgate\SDK\Entity\Response\SingleTransferResponse;
use Comgate\SDK\Entity\Response\TransferListResponse;
use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Http\Response;
use DateTimeInterface;

class Client
{

	/** @var ITransport */
	protected $transport;

	public function __construct(ITransport $transport)
	{
		$this->transport = $transport;
	}

	public function createPayment(Payment $payment): PaymentCreateResponse
	{
		$paymentCreateRequest = new PaymentCreateRequest($payment);
		$paymentCreateResponse = $this->transport->post($paymentCreateRequest->getUrn(),
			$paymentCreateRequest->toArray());
		return new PaymentCreateResponse($paymentCreateResponse);
	}

	public function getStatus(string $transId): PaymentStatusResponse
	{
		$paymentStatusRequest = new PaymentStatusRequest($transId);
		$statusResponse = $this->transport->post($paymentStatusRequest->getUrn(), $paymentStatusRequest->toArray());
		return new PaymentStatusResponse($statusResponse);
	}

	public function getMethods(): MethodsResponse
	{
		$methodsRequest = new MethodsRequest();
		$methodsResponse = $this->transport->post($methodsRequest->getUrn(), $methodsRequest->toArray());

		return new MethodsResponse($methodsResponse);
	}

	public function cancelPayment(string $transId): PaymentCancelResponse
	{
		$cancelPaymentRequest = new PaymentCancelRequest($transId);
		$cancelResponse = $this->transport->post($cancelPaymentRequest->getUrn(), $cancelPaymentRequest->toArray());
		return new PaymentCancelResponse($cancelResponse);
	}

	public function capturePreauth(string $transId, Money $amount): PreauthCaptureResponse
	{
		$capturePreauthRequest = new PreauthCaptureRequest($transId, $amount);
		$captureResponse = $this->transport->post($capturePreauthRequest->getUrn(), $capturePreauthRequest->toArray());
		return new PreauthCaptureResponse($captureResponse);
	}

	public function cancelPreauth(string $transId): PreauthCancelResponse
	{
		$cancelPreauthRequest = new PreauthCancelRequest($transId);
		$cancelResponse = $this->transport->post($cancelPreauthRequest->getUrn(), $cancelPreauthRequest->toArray());
		return new PreauthCancelResponse($cancelResponse);
	}

	public function refundPayment(Refund $refund): RefundResponse
	{
		$refundRequest = new PaymentRefundRequest($refund);
		$refundResponse = $this->transport->post($refundRequest->getUrn(), $refundRequest->toArray());
		return new RefundResponse($refundResponse);
	}

	public function initRecurringPayment(Payment $payment): RecurringPaymentResponse
	{
		$recurringRequest = new RecurringPaymentRequest($payment);
		$recurringResponse = $this->transport->post($recurringRequest->getUrn(), $recurringRequest->toArray());
		return new RecurringPaymentResponse($recurringResponse);
	}

	/**
	 * @param array<string, string> $params
	 * @return SimulationResponse
	 * @throws Exception\ApiException
	 */
	public function simulation(array $params): SimulationResponse
	{
		$simulationRequest = new SimulationRequest($params);
		$simulationResponse = $this->transport->post($simulationRequest->getUrn(), $simulationRequest->toArray());
		return new SimulationResponse($simulationResponse);
	}

	/**
	 * @param DateTimeInterface $date
	 * @param bool $test
	 * @return TransferListResponse
	 * @see https://help.comgate.cz/docs/api-protokol#seznam-p%C5%99evod%C5%AF-v-r%C3%A1mci-dne
	 */
	public function transferList(DateTimeInterface $date, bool $test): TransferListResponse
	{
		$transferListRequest = new TransferListRequest($date, $test);
		$transferListResponse = $this->transport->post($transferListRequest->getUrn(), $transferListRequest->toArray());
		return new TransferListResponse($transferListResponse);
	}

	public function singleTransfer(int $transferId, bool $test): SingleTransferResponse
	{
		$singleTransferRequest = new SingleTransferRequest(1, true);
		$singleTransferResponse = $this->transport->post($singleTransferRequest->getUrn(),
			$singleTransferRequest->toArray());
		return new SingleTransferResponse($singleTransferResponse);
	}

	public function csvSingleTransfer(string $transferId, bool $test): CsvSingleTransferResponse
	{
		$csvSingleTransferRequest = new CsvSingleTransferRequest($transferId, $test);
		$csvSingleTransferResponse = $this->transport->post($csvSingleTransferRequest->getUrn(),
			$csvSingleTransferRequest->toArray());
		return new CsvSingleTransferResponse($csvSingleTransferResponse);
	}

	public function aboSingleTransfer(string $transferId, bool $test, string $type, string $encoding): AboSingleTransferResponse
	{
		$aboSingleTransferRequest = new AboSingleTransferRequest($transferId, $test, $type, $encoding);
		$aboSingleTransferResponse = $this->transport->post($aboSingleTransferRequest->getUrn(),
			$aboSingleTransferRequest->toArray());
		return new AboSingleTransferResponse($aboSingleTransferResponse);
	}

	/**
	 * @return ITransport
	 */
	public function getTransport(): ITransport
	{
		return $this->transport;
	}

	/**
	 * @param ITransport $transport
	 * @return Client
	 */
	public function setTransport(ITransport $transport): Client
	{
		$this->transport = $transport;
		return $this;
	}
}

