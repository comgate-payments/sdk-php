<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\PaymentStatus;
use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Http\Response;

class Client
{

	protected ITransport $transport;

	public function __construct(ITransport $transport)
	{
		$this->transport = $transport;
	}

	public function createPayment(Payment $payment): Response
	{
		return $this->transport->post('create', $payment->toArray());
	}

	public function getStatus(PaymentStatus $status): Response
	{
		return $this->transport->post('status', $status->toArray());
	}

}
