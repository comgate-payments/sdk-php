<?php
declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Entity\Method;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Http\Response;
use GuzzleHttp\Psr7\Query;
use Comgate\SDK\Exception\Api\PaymentNotFoundException;
use Comgate\SDK\Exception\ApiException;

class PaymentStatusResponse
{
	protected int $code;
	protected string $message;
	protected string $merchant;
	protected bool $test;
	protected Money $price;
	protected string $curr;
	protected string $label;
	protected string $refId;
	protected string $payerId;
	protected string $method;
	protected string $account;
	protected string $email;
	protected string $name;
	protected string $transId;
	protected string $secret;
	protected string $status;
	protected string $payerName;
	protected string $fee;

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
                            ->setTest($parsedResponse['test'] === 'true')
                            ->setPrice(Money::ofCents((int) $parsedResponse['price']))
                            ->setCurrency($parsedResponse['curr'])
                            ->setLabel($parsedResponse['label'])
                            ->setRefId($parsedResponse['refId'])
                            ->setPayerId($parsedResponse['payerId'] ?? '')
                            ->setMethod($parsedResponse['method'])
                            ->setAccount($parsedResponse['account'] ?? '')
                            ->setEmail($parsedResponse['email'])
                            ->setName($parsedResponse['name'])
                            ->setTransId($parsedResponse['transId'])
                            ->setSecret($parsedResponse['secret'])
                            ->setStatus($parsedResponse['status'])
                            ->setPayerName($parsedResponse['payerName'])
                            ->setFee($parsedResponse['fee']);

                        break;

                    case 1400:
                            throw new PaymentNotFoundException($message, $code);

                    default:
                            throw new ApiException($message, $code);
		}
	}

         /**
         *
         * @return array<string, bool|int|string>
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
			'transId' => $this->getTransId(),
			'secret' => $this->getSecret(),
			'status' => $this->getStatus(),
			'payerName' => $this->getPayerName(),
			'fee' => $this->getFee(),
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
}
