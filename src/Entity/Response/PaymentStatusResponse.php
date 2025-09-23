<?php
declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Entity\Method;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Http\Response;
use Comgate\SDK\Http\Query;
use Comgate\SDK\Exception\Api\PaymentNotFoundException;
use Comgate\SDK\Exception\ApiException;

class PaymentStatusResponse
{
	/**
	 * @var int
	 */
	protected $code;
	/**
	 * @var string
	 */
	protected $message;
	/**
	 * @var bool
	 */
	protected $test;
	/**
	 * @var Money
	 */
	protected $price;
	/**
	 * @var string
	 */
	protected $curr;
	/**
	 * @var string
	 */
	protected $label;
	/**
	 * @var string
	 */
	protected $refId;
	/**
	 * @var string
	 */
	protected $payerId;
	/**
	 * @var string
	 */
	protected $method;
	/**
	 * @var string
	 */
	protected $account;
	/**
	 * @var string
	 */
	protected $email;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $phone;
	/**
	 * @var string
	 */
	protected $transId;
	/**
	 * @var string
	 */
	protected $status;
	/**
	 * @var string
	 */
	protected $payerName;
	/**
	 * @var string
	 */
	protected $payerAcc;
	/**
	 * @var string
	 */
	protected $fee;
	/**
	 * @var string
	 */
	protected $vs;
	/**
	 * @var string
	 */
	protected $cardValid;
	/**
	 * @var string
	 */
	protected $cardNumber;
	/**
	 * @var string
	 */
	protected $appliedFee;
	/**
	 * @var string
	 */
	protected $appliedFeeTyp;
	/**
	 * @var string
	 */
	protected $paymentErrorReason;
	/**
	 * @var string
	 */
	protected $merchant;
	/**
	 * @var string
	 */
	protected $secret;

	/**
	 * @param Response $paymentStatusResponse
	 * @return PaymentStatusResponse
	 */
	public function __construct(Response $paymentStatusResponse)
	{
		$parsedResponse = Query::parse($paymentStatusResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'];

                switch ($code) {
                    case 0:
                        $this->setCode($code)
                            ->setMessage($message)
                            ->setMerchant($parsedResponse['merchant'])
							->setSecret($parsedResponse['secret'])
							->setTransId($parsedResponse['transId'])
							->setTest($parsedResponse['test'] === 'true')
							->setPrice(Money::ofCents((int) $parsedResponse['price']))
							->setCurrency($parsedResponse['curr'])
							->setLabel($parsedResponse['label'])
							->setRefId($parsedResponse['refId'])
							->setPayerId($parsedResponse['payerId'] ?? '')
							->setMethod($parsedResponse['method'])
							->setAccount($parsedResponse['account'] ?? '')
							->setEmail($parsedResponse['email'] ?? '')
							->setName($parsedResponse['name'])
							->setPhone($parsedResponse['phone'] ?? '')
                            ->setStatus($parsedResponse['status'])
                            ->setPayerName($parsedResponse['payerName'])
                            ->setPayerAcc($parsedResponse['payerAcc'] ?? '')
                            ->setFee($parsedResponse['fee'] ?? '')
                            ->setVs($parsedResponse['vs'] ?? '')
                            ->setCardValid($parsedResponse['cardValid'] ?? '')
                            ->setCardNumber($parsedResponse['cardNumber'])
                            ->setAppliedFee($parsedResponse['appliedFee'] ?? '')
                            ->setAppliedFeeTyp($parsedResponse['appliedFeeTyp'] ?? '')
                            ->setPaymentErrorReason($parsedResponse['paymentErrorReason'] ?? '');

                        break;

                    case 1400:
                            throw new PaymentNotFoundException($message, $code);

                    default:
                            throw new ApiException($message, $code);
		}
	}

	/**
	 * @return array<string, bool|string|int|null>
	 */
	public function toArray(): array
	{
		$output = [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'merchant' => $this->getMerchant(),
			'test' => $this->isTest(),
			'price' => $this->getPrice()->get(),
			'curr' => $this->getCurrency(),
			'label' => $this->getLabel(),
			'refId' => $this->getRefId(),
			'payerId' => $this->getPayerId(),
			'method' => $this->getMethod(),
			'account' => $this->getAccount(),
			'email' => $this->getEmail(),
			'name' => $this->getName(),
			'phone' => $this->getPhone(),
			'transId' => $this->getTransId(),
			'secret' => $this->getSecret(),
			'status' => $this->getStatus(),
			'payerName' => $this->getPayerName(),
			'payerAcc' => $this->getPayerAcc(),
			'fee' => $this->getFee(),
			'vs' => $this->getVs(),
			'cardValid' => $this->getCardValid(),
			'cardNumber' => $this->getCardNumber(),
			'appliedFee' => $this->getAppliedFee(),
			'appliedFeeTyp' => $this->getAppliedFeeTyp(),
			'paymentErrorReason' => $this->getPaymentErrorReason()
		];

		return $output;
	}

	/**
	 * @return int
	 */
	public function getCode(): int
	{
		return $this->code;
	}

	/**
	 * @param int $code
	 * @return PaymentStatusResponse
	 */
	public function setCode(int $code): PaymentStatusResponse
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 * @return PaymentStatusResponse
	 */
	public function setMessage(string $message): PaymentStatusResponse
	{
		$this->message = $message;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMerchant(): string
	{
		return $this->merchant;
	}

	/**
	 * @param string $merchant
	 * @return PaymentStatusResponse
	 */
	public function setMerchant(string $merchant): PaymentStatusResponse
	{
		$this->merchant = $merchant;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isTest(): bool
	{
		return $this->test;
	}

	/**
	 * @param bool $test
	 * @return PaymentStatusResponse
	 */
	public function setTest(bool $test): PaymentStatusResponse
	{
		$this->test = $test;
		return $this;
	}

	/**
	 * @return Money
	 */
	public function getPrice(): Money
	{
		return $this->price;
	}

	/**
	 * @param Money $price
	 * @return PaymentStatusResponse
	 */
	public function setPrice(Money $price): PaymentStatusResponse
	{
		$this->price = $price;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrency(): string
	{
		return $this->curr;
	}

	/**
	 * @param string $curr
	 * @return PaymentStatusResponse
	 */
	public function setCurrency(string $curr): PaymentStatusResponse
	{
		$this->curr = $curr;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLabel(): string
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 * @return PaymentStatusResponse
	 */
	public function setLabel(string $label): PaymentStatusResponse
	{
		$this->label = $label;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRefId(): string
	{
		return $this->refId;
	}

	/**
	 * @param string $refId
	 * @return PaymentStatusResponse
	 */
	public function setRefId(string $refId): PaymentStatusResponse
	{
		$this->refId = $refId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPayerId(): string
	{
		return $this->payerId;
	}

	/**
	 * @param string $payerId
	 * @return PaymentStatusResponse
	 */
	public function setPayerId(string $payerId): PaymentStatusResponse
	{
		$this->payerId = $payerId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 * @return PaymentStatusResponse
	 */
	public function setMethod(string $method): PaymentStatusResponse
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAccount(): string
	{
		return $this->account;
	}

	/**
	 * @param string $account
	 * @return PaymentStatusResponse
	 */
	public function setAccount(string $account): PaymentStatusResponse
	{
		$this->account = $account;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return PaymentStatusResponse
	 */
	public function setEmail(string $email): PaymentStatusResponse
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return PaymentStatusResponse
	 */
	public function setName(string $name): PaymentStatusResponse
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPhone(): string
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 * @return PaymentStatusResponse
	 */
	public function setPhone(string $phone): PaymentStatusResponse
	{
		$this->phone = $phone;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getTransId(): string
	{
		return $this->transId;
	}

	/**
	 * @param string $transId
	 * @return PaymentStatusResponse
	 */
	public function setTransId(string $transId): PaymentStatusResponse
	{
		$this->transId = $transId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSecret(): string
	{
		return $this->secret;
	}

	/**
	 * @param string $secret
	 * @return PaymentStatusResponse
	 */
	public function setSecret(string $secret): PaymentStatusResponse
	{
		$this->secret = $secret;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 * @return PaymentStatusResponse
	 */
	public function setStatus(string $status): PaymentStatusResponse
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPayerName(): string
	{
		return $this->payerName;
	}

	/**
	 * @param string $payerName
	 * @return PaymentStatusResponse
	 */
	public function setPayerName(string $payerName): PaymentStatusResponse
	{
		$this->payerName = $payerName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPayerAcc(): string
	{
		return $this->payerAcc;
	}

	/**
	 * @param string $payerAcc
	 * @return PaymentStatusResponse
	 */
	public function setPayerAcc(string $payerAcc): PaymentStatusResponse
	{
		$this->payerAcc = $payerAcc;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFee(): string
	{
		return $this->fee;
	}

	/**
	 * @param string $fee
	 * @return PaymentStatusResponse
	 */
	public function setFee(string $fee): PaymentStatusResponse
	{
		$this->fee = $fee;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVs(): string
	{
		return $this->vs;
	}

	/**
	 * @param string $vs
	 * @return PaymentStatusResponse
	 */
	public function setVs(string $vs): PaymentStatusResponse
	{
		$this->vs = $vs;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCardValid(): string
	{
		return $this->cardValid;
	}

	/**
	 * @param string $cardValid
	 * @return PaymentStatusResponse
	 */
	public function setCardValid(string $cardValid): PaymentStatusResponse
	{
		$this->cardValid = $cardValid;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCardNumber(): string
	{
		return $this->cardNumber;
	}

	/**
	 * @param string $cardNumber
	 * @return PaymentStatusResponse
	 */
	public function setCardNumber(string $cardNumber): PaymentStatusResponse
	{
		$this->cardNumber = $cardNumber;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAppliedFee(): string
	{
		return $this->appliedFee;
	}

	/**
	 * @param string $appliedFee
	 * @return PaymentStatusResponse
	 */
	public function setAppliedFee(string $appliedFee): PaymentStatusResponse
	{
		$this->appliedFee = $appliedFee;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAppliedFeeTyp(): string
	{
		return $this->appliedFeeTyp;
	}

	/**
	 * @param string $appliedFeeTyp
	 * @return PaymentStatusResponse
	 */
	public function setAppliedFeeTyp(string $appliedFeeTyp): PaymentStatusResponse
	{
		$this->appliedFeeTyp = $appliedFeeTyp;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaymentErrorReason(): string
	{
		return $this->paymentErrorReason;
	}

	/**
	 * @param string $paymentErrorReason
	 * @return PaymentStatusResponse
	 */
	public function setPaymentErrorReason(string $paymentErrorReason): PaymentStatusResponse
	{
		$this->paymentErrorReason = $paymentErrorReason;
		return $this;
	}

}
