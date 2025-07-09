<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Comgate\SDK\Http\Query;

class MotoPaymentCreateResponse
{

	protected int $code;

	protected string $message = '';

	protected string $transId = '';

	protected string $status = '';

	/**
	 * @param Response $motoPaymentsCreateResponse
	 * @throws ApiException
	 * @throws MissingParamException
	 */
	public function __construct(Response $motoPaymentsCreateResponse)
	{
		$parsedResponse = Query::parse($motoPaymentsCreateResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'];

		switch ($code) {
			case 0:
				$this->setCode($code)
					->setMessage($message)
					->setTransId($parsedResponse['transId'])
					->setStatus($parsedResponse['status']);

				break;

			case 1400:
				throw new MissingParamException($message, $code);

			default:
				if (isset($parsedResponse['transId'])) {
					$message .= ' - ' . $parsedResponse['transId'];
				}
				if (isset($parsedResponse['status'])) {
					$message .= ' - ' . $parsedResponse['status'];
				}
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
			'status' => $this->getStatus(),
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
	 * @return MotoPaymentCreateResponse
	 */
	public function setCode(int $code): MotoPaymentCreateResponse
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
	 * @return MotoPaymentCreateResponse
	 */
	public function setMessage(string $message): MotoPaymentCreateResponse
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
	 * @return MotoPaymentCreateResponse
	 */
	public function setTransId(string $transId): MotoPaymentCreateResponse
	{
		$this->transId = $transId;
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
	 * @return MotoPaymentCreateResponse
	 */
	public function setStatus(string $status): MotoPaymentCreateResponse
	{
		$this->status = $status;
		return $this;
	}
}
