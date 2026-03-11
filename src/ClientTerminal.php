<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Entity\TerminalPayment;
use Comgate\SDK\Entity\TerminalRefund;
use Comgate\SDK\Entity\Request\TerminalPaymentCreateRequest;
use Comgate\SDK\Entity\Request\TerminalPaymentStatusRequest;
use Comgate\SDK\Entity\Request\TerminalPaymentCancelRequest;
use Comgate\SDK\Entity\Request\TerminalClosingRequest;
use Comgate\SDK\Entity\Request\TerminalRefundCreateRequest;
use Comgate\SDK\Entity\Request\TerminalRefundStatusRequest;
use Comgate\SDK\Entity\Request\TerminalRefundCancelRequest;
use Comgate\SDK\Entity\Request\TerminalStatusRequest;
use Comgate\SDK\Entity\Response\TerminalPaymentCreateResponse;
use Comgate\SDK\Entity\Response\TerminalPaymentStatusResponse;
use Comgate\SDK\Entity\Response\TerminalPaymentCancelResponse;
use Comgate\SDK\Entity\Response\TerminalClosingResponse;
use Comgate\SDK\Entity\Response\TerminalRefundCreateResponse;
use Comgate\SDK\Entity\Response\TerminalRefundStatusResponse;
use Comgate\SDK\Entity\Response\TerminalRefundCancelResponse;
use Comgate\SDK\Entity\Response\TerminalStatusResponse;
use Comgate\SDK\Http\ITransport;

class ClientTerminal
{
	/** @var ITransport */
	protected $transport;

	public function __construct(ITransport $transport)
	{
		$this->transport = $transport;
	}

	/**
	 * Vytvoří novou platbu na terminálu
	 */
	public function createPayment(TerminalPayment $terminalPayment): TerminalPaymentCreateResponse
	{
		$request = new TerminalPaymentCreateRequest($terminalPayment);
		$response = $this->transport->postJson($request->getUrn(), $request->toArray());
		return new TerminalPaymentCreateResponse($response);
	}

	/**
	 * Zjistí stav platby na terminálu
	 */
	public function getPaymentStatus(string $transId): TerminalPaymentStatusResponse
	{
		$request = new TerminalPaymentStatusRequest($transId);
		$response = $this->transport->get($this->replacePathParam($request->getUrn(), $transId));
		return new TerminalPaymentStatusResponse($response);
	}

	/**
	 * Zruší platbu na terminálu
	 */
	public function cancelPayment(string $transId): TerminalPaymentCancelResponse
	{
		$request = new TerminalPaymentCancelRequest($transId);
		$response = $this->transport->delete($this->replacePathParam($request->getUrn(), $transId));
		return new TerminalPaymentCancelResponse($response);
	}

	/**
	 * Provede uzávěrku na terminálu
	 */
	public function createClosing(): TerminalClosingResponse
	{
		$request = new TerminalClosingRequest();
		$response = $this->transport->postJson($request->getUrn(), $request->toArray());
		return new TerminalClosingResponse($response);
	}

	/**
	 * Vytvoří nový návrat (refund) na terminálu
	 */
	public function createRefund(TerminalRefund $terminalRefund): TerminalRefundCreateResponse
	{
		$request = new TerminalRefundCreateRequest($terminalRefund);
		$response = $this->transport->postJson($request->getUrn(), $request->toArray());
		return new TerminalRefundCreateResponse($response);
	}

	/**
	 * Zjistí stav návratu na terminálu
	 */
	public function getRefundStatus(string $transId): TerminalRefundStatusResponse
	{
		$request = new TerminalRefundStatusRequest($transId);
		$response = $this->transport->get($this->replacePathParam($request->getUrn(), $transId));
		return new TerminalRefundStatusResponse($response);
	}

	/**
	 * Zruší návrat na terminálu
	 */
	public function cancelRefund(string $transId): TerminalRefundCancelResponse
	{
		$request = new TerminalRefundCancelRequest($transId);
		$response = $this->transport->delete($this->replacePathParam($request->getUrn(), $transId));
		return new TerminalRefundCancelResponse($response);
	}

	/**
	 * Zjistí stav terminálu
	 */
	public function getTerminalStatus(): TerminalStatusResponse
	{
		$request = new TerminalStatusRequest();
		$response = $this->transport->get($request->getUrn());
		return new TerminalStatusResponse($response);
	}

	/**
	 * Nahradí {transId} v URN skutečným transId
	 */
	private function replacePathParam(string $urn, string $transId): string
	{
		return str_replace('{transId}', $transId, $urn);
	}
}
