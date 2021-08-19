<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\Request\PaymentCreateRequest;
use Comgate\SDK\Entity\Request\PaymentStatusRequest;
use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Http\Response;

class Client
{

	/** @var ITransport */
	protected $transport;

	public function __construct(ITransport $transport)
	{
		$this->transport = $transport;
	}

	public function createPayment(Payment $payment): Response
	{
		return $this->transport->post('create', PaymentCreateRequest::of($payment)->toArray());
	}

	public function getStatus(Payment $payment): Response
	{
		return $this->transport->post('status', PaymentStatusRequest::of($payment)->toArray());
	}

}
