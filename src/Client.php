<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\PaymentStatus;
use Comgate\SDK\Http\Response;
use Comgate\SDK\Http\Transport;

class Client
{

	protected Transport $transport;

	public function __construct(Transport $transport)
	{
		$this->transport = $transport;
	}

	public function create(Payment $payment): Response
	{
		return $this->transport->post('create', $payment->toArray());
	}

	public function status(PaymentStatus $status): Response
	{
		return $this->transport->post('status', $status->toArray());
	}

}
