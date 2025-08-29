<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\MotoPayment;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\PaymentCard;
use Comgate\SDK\Entity\Refund;
use Comgate\SDK\Entity\Request\AboDownloadRequest;
use Comgate\SDK\Entity\Request\AboSingleTransferRequest;
use Comgate\SDK\Entity\Request\AppleDomainAssociationRequest;
use Comgate\SDK\Entity\Request\CsvDownloadRequest;
use Comgate\SDK\Entity\Request\CsvSingleTransferRequest;
use Comgate\SDK\Entity\Request\MotoPaymentCreateRequest;
use Comgate\SDK\Entity\Request\PaymentCreateRequest;
use Comgate\SDK\Entity\Request\MethodsRequest;
use Comgate\SDK\Entity\Request\PaymentCancelRequest;
use Comgate\SDK\Entity\Request\PaymentRefundRequest;
use Comgate\SDK\Entity\Request\PreauthCancelRequest;
use Comgate\SDK\Entity\Request\PaymentStatusRequest;
use Comgate\SDK\Entity\Request\PreauthCaptureRequest;
use Comgate\SDK\Entity\Request\PublicCryptoKeyRequest;
use Comgate\SDK\Entity\Request\RecurringPaymentRequest;
use Comgate\SDK\Entity\Request\SimulationRequest;
use Comgate\SDK\Entity\Request\SingleTransferRequest;
use Comgate\SDK\Entity\Request\TransferListRequest;
use Comgate\SDK\Entity\Response\AboDownloadResponse;
use Comgate\SDK\Entity\Response\AboSingleTransferResponse;
use Comgate\SDK\Entity\Response\AppleDomainAssociationResponse;
use Comgate\SDK\Entity\Response\CsvDownloadResponse;
use Comgate\SDK\Entity\Response\CsvSingleTransferResponse;
use Comgate\SDK\Entity\Response\MethodsResponse;
use Comgate\SDK\Entity\Response\MotoPaymentCreateResponse;
use Comgate\SDK\Entity\Response\PaymentCancelResponse;
use Comgate\SDK\Entity\Response\PaymentCreateResponse;
use Comgate\SDK\Entity\Response\PaymentStatusResponse;
use Comgate\SDK\Entity\Response\PreauthCancelResponse;
use Comgate\SDK\Entity\Response\PreauthCaptureResponse;
use Comgate\SDK\Entity\Response\PublicCryptoKeyResponse;
use Comgate\SDK\Entity\Response\RecurringPaymentResponse;
use Comgate\SDK\Entity\Response\RefundResponse;
use Comgate\SDK\Entity\Response\SimulationResponse;
use Comgate\SDK\Entity\Response\SingleTransferResponse;
use Comgate\SDK\Entity\Response\TransferListResponse;
use Comgate\SDK\Http\ITransport;
use DateTimeInterface;
use Exception;
use phpseclib3\Crypt\RSA;

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

	public function getMethods(?MethodsRequest $methodsRequest = null): MethodsResponse
	{
		if (($methodsRequest instanceof MethodsRequest) === false) {
			$methodsRequest = new MethodsRequest();
		}

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
         *
         * @param array<string, string> $params
         * @return SimulationResponse
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
		$singleTransferRequest = new SingleTransferRequest($transferId, $test);
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

	public function getAppleDomainAssociation(string $method = ''): AppleDomainAssociationResponse{
		$appleDomainAssociationRequest = new AppleDomainAssociationRequest($method);
		$appleDomainAssociationResponse = $this->transport->post($appleDomainAssociationRequest->getUrn(),
			$appleDomainAssociationRequest->toArray());
		return new AppleDomainAssociationResponse($appleDomainAssociationResponse);
	}
	public function getCsvDownload(string $date, bool $test = false): void{
		$csvDownloadRequest = new CsvDownloadRequest($date, $test);
		$csvDownloadResponse = $this->transport->post($csvDownloadRequest->getUrn(),
			$csvDownloadRequest->toArray());

		new CsvDownloadResponse($csvDownloadResponse);
	}

	public function getAboDownload(string $date, string $type = '',  bool $test = false, string $encoding = 'utf8'): void{
		$aboDownloadRequest = new AboDownloadRequest($date, $type, $test, $encoding);
		$aboDownloadResponse = $this->transport->post($aboDownloadRequest->getUrn(),
			$aboDownloadRequest->toArray());

		new AboDownloadResponse($aboDownloadResponse);
	}

	/**
	 * Method only for specific merchant, who are PCI DSS certified
	 * @param Payment $payment
	 * @param PaymentCard $paymentCard
	 * @return MotoPaymentCreateResponse|null
	 * @throws Exception
	 */
	public function createMotoPayment(Payment $payment, PaymentCard $paymentCard): ?MotoPaymentCreateResponse
	{
		$publicCryptoKeyRequest = new PublicCryptoKeyRequest();
		$publicCryptoKeyResponse = new PublicCryptoKeyResponse($this->transport->post($publicCryptoKeyRequest->getUrn(), $publicCryptoKeyRequest->toArray()));

		$publicJkwKey = null;
		$jwkData = json_decode(base64_decode($publicCryptoKeyResponse->getKey(), true), true);
		if (isset($jwkData['jwk'])){
			$publicJkwKey = json_encode($jwkData['jwk']);
		}
		if (is_null($publicJkwKey) || $publicJkwKey == '') {
			throw new Exception('No public encryption key for encrypting card data');
		}

		/** @var \phpseclib3\Crypt\RSA\PublicKey $rsa */
		$rsa = RSA::loadPublicKey($publicJkwKey);

		$motoPayment = new MotoPayment();
		$motoPayment->setParams($payment->getParams());

		if (!is_null($paymentCard->getCardNumber()) && $paymentCard->getCardNumber() !== '') {
			$motoPayment->setEncryptedCardNumber(base64_encode($rsa->encrypt($paymentCard->getCardNumber())));
		} else {
			throw new Exception('No card number for encrypting card data');
		}

		if (!is_null($paymentCard->getCardExpiration()) && $paymentCard->getCardExpiration() !== '') {
			$motoPayment->setEncryptedCardExpiration(base64_encode($rsa->encrypt($paymentCard->getCardExpiration())));
		} else {
			throw new Exception('No card expiration for encrypting card data');
		}

		if (!is_null($paymentCard->getCardCvv()) && $paymentCard->getCardCvv() !== '') {
			$motoPayment->setEncryptedCardCvv(base64_encode($rsa->encrypt($paymentCard->getCardCvv())));
		}

		$motoPaymentCreateRequest = new MotoPaymentCreateRequest($motoPayment);
		$motoPaymentCreateResponse = $this->transport->post($motoPaymentCreateRequest->getUrn(), $motoPaymentCreateRequest->toArray());
		return new MotoPaymentCreateResponse($motoPaymentCreateResponse);
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

