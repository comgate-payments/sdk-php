<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use GuzzleHttp\Psr7\Query;

class PaymentCreateResponse
{

	protected int $code;

	protected string $message = '';

	protected string $transId = '';

	protected string $redirect = '';

	protected string $applepay = '';

	protected string $applepayMessage = '';

	public function __construct(Response $paymentsCreateResponse)
	{
		$parsedResponse = Query::parse($paymentsCreateResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'];

		switch ($code) {
			case 0:
				$this->setCode($code)
					->setMessage($message)
					->setTransId($parsedResponse['transId'])
					->setRedirect($parsedResponse['redirect']);

				if(isset($parsedResponse['applepay'])){
					$this->setApplepay($parsedResponse['applepay']);
				}

				if(isset($parsedResponse['applepayMessage'])){
					$this->setApplepayMessage($parsedResponse['applepayMessage']);
				}

				break;

			case 1400:
				throw new MissingParamException($message, $code);
				break;

			default:
				throw new ApiException($message, $code);
				break;
		}

		return $this;
	}

	public function toArray()
	{
		return [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'transId' => $this->getTransId(),
			'redirect' => $this->getRedirect(),
			'applepay' => $this->getApplepay(),
			'applepayMessage' => $this->getApplepayMessage(),
		];
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
	 * @return PaymentCreateResponse
	 */
	public function setCode(int $code): PaymentCreateResponse
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
	 * @return PaymentCreateResponse
	 */
	public function setMessage(string $message): PaymentCreateResponse
	{
		$this->message = $message;
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
	 * @return PaymentCreateResponse
	 */
	public function setTransId(string $transId): PaymentCreateResponse
	{
		$this->transId = $transId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRedirect(): string
	{
		return $this->redirect;
	}

	/**
	 * @param string $redirect
	 * @return PaymentCreateResponse
	 */
	public function setRedirect(string $redirect): PaymentCreateResponse
	{
		$this->redirect = $redirect;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getApplepay(): string
	{
		return $this->applepay;
	}

	/**
	 * @param string $applepay
	 * @return PaymentCreateResponse
	 */
	public function setApplepay(string $applepay): PaymentCreateResponse
	{
		$this->applepay = $applepay;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getApplepayMessage(): string
	{
		return $this->applepayMessage;
	}

	/**
	 * @param string $applepayMessage
	 * @return PaymentCreateResponse
	 */
	public function setApplepayMessage(string $applepayMessage): PaymentCreateResponse
	{
		$this->applepayMessage = $applepayMessage;
		return $this;
	}
}
