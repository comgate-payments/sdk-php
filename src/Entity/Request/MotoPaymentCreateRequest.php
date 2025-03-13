<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\MotoPayment;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\LogicalException;

class MotoPaymentCreateRequest extends PaymentCreateRequest
{
	public function __construct(MotoPayment $payment)
	{
		parent::__construct($payment);
	}

	/**
	 * @return string
	 */
	public function getUrn(): string
	{
		return 'moto';
	}

	/**
	 * @return array<string, bool|string|int|null>
	 */
	public function toArray(): array
	{
		$output = parent::toArray();

		$output['embedded'] = $this->payment->isEmbedded() ? 'true' : 'false';
		$output['initRecurring'] = $this->payment->isInitRecurring() ? 'true' : 'false';
		$output['dynamicExpiration'] = $this->payment->isDynamicExpiration() ? 'true' : 'false';

		return $output;
	}

}




