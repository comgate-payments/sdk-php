<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class TerminalPaymentStatusResponse
{
	/** @var int */
	protected $code;

	/** @var string */
	protected $message = '';

	/** @var int */
	protected $price = 0;

	/** @var string */
	protected $curr = '';

	/** @var string */
	protected $refId = '';

	/** @var string */
	protected $transId = '';

	/** @var string */
	protected $status = '';

	/** @var string */
	protected $fee = '';

	/** @var string */
	protected $cardValid = '';

	/** @var string */
	protected $cardNumber = '';

	/** @var string */
	protected $paymentErrorReason = '';

	/** @var bool */
	protected $reversed = false;

	/** @var string */
	protected $amountRefunded = '';

	/**
	 * @param Response $response
	 * @throws ApiException
	 */
	public function __construct(Response $response)
	{
		$data = json_decode($response->getContent(), true);

		$code = (int) ($data['code'] ?? 1500);
		$message = $data['message'] ?? '';

		switch ($code) {
			case 0:
				$this->setCode($code)
					->setMessage($message)
					->setPrice((int) ($data['price'] ?? 0))
					->setCurr($data['curr'] ?? '')
					->setRefId($data['refId'] ?? '')
					->setTransId($data['transId'] ?? '')
					->setStatus($data['status'] ?? '')
					->setFee($data['fee'] ?? '')
					->setCardValid($data['cardValid'] ?? '')
					->setCardNumber($data['cardNumber'] ?? '')
					->setPaymentErrorReason($data['paymentErrorReason'] ?? '')
					->setReversed((bool) ($data['reversed'] ?? false))
					->setAmountRefunded($data['amountRefunded'] ?? '');
				break;

			default:
				throw new ApiException($message, $code);
		}
	}

	/**
	 * @return array<string, string|int|bool>
	 */
	public function toArray(): array
	{
		return [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'price' => $this->getPrice(),
			'curr' => $this->getCurr(),
			'refId' => $this->getRefId(),
			'transId' => $this->getTransId(),
			'status' => $this->getStatus(),
			'fee' => $this->getFee(),
			'cardValid' => $this->getCardValid(),
			'cardNumber' => $this->getCardNumber(),
			'paymentErrorReason' => $this->getPaymentErrorReason(),
			'reversed' => $this->isReversed(),
			'amountRefunded' => $this->getAmountRefunded(),
		];
	}

	public function getCode(): int { return $this->code; }
	public function setCode(int $code): self { $this->code = $code; return $this; }
	public function getMessage(): string { return $this->message; }
	public function setMessage(string $message): self { $this->message = $message; return $this; }
	public function getPrice(): int { return $this->price; }
	public function setPrice(int $price): self { $this->price = $price; return $this; }
	public function getCurr(): string { return $this->curr; }
	public function setCurr(string $curr): self { $this->curr = $curr; return $this; }
	public function getRefId(): string { return $this->refId; }
	public function setRefId(string $refId): self { $this->refId = $refId; return $this; }
	public function getTransId(): string { return $this->transId; }
	public function setTransId(string $transId): self { $this->transId = $transId; return $this; }
	public function getStatus(): string { return $this->status; }
	public function setStatus(string $status): self { $this->status = $status; return $this; }
	public function getFee(): string { return $this->fee; }
	public function setFee(string $fee): self { $this->fee = $fee; return $this; }
	public function getCardValid(): string { return $this->cardValid; }
	public function setCardValid(string $cardValid): self { $this->cardValid = $cardValid; return $this; }
	public function getCardNumber(): string { return $this->cardNumber; }
	public function setCardNumber(string $cardNumber): self { $this->cardNumber = $cardNumber; return $this; }
	public function getPaymentErrorReason(): string { return $this->paymentErrorReason; }
	public function setPaymentErrorReason(string $paymentErrorReason): self { $this->paymentErrorReason = $paymentErrorReason; return $this; }
	public function isReversed(): bool { return $this->reversed; }
	public function setReversed(bool $reversed): self { $this->reversed = $reversed; return $this; }
	public function getAmountRefunded(): string { return $this->amountRefunded; }
	public function setAmountRefunded(string $amountRefunded): self { $this->amountRefunded = $amountRefunded; return $this; }
}
