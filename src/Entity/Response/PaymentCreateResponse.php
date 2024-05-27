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

	/**
	 * @param Response $paymentsCreateResponse
	 * @throws ApiException
	 * @throws MissingParamException
	 */
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

				break;

			case 1400:
				throw new MissingParamException($message, $code);

			default:
				throw new ApiException($message, $code);
		}
	}

	/**
	 * @return array<string, string|int>
	 */
	public function toArray(): array
	{
		return [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'transId' => $this->getTransId(),
			'redirect' => $this->getRedirect(),
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
}
