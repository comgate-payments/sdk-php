<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use GuzzleHttp\Psr7\Query;

class RecurringPaymentResponse
{

	protected int $code;

	protected string $message;

	protected string $transId;

	/**
	 * @param Response $recurringPaymentResponse
	 * @throws ApiException
	 * @throws MissingParamException
	 */
	public function __construct(Response $recurringPaymentResponse)
	{
		$parsedResponse = Query::parse($recurringPaymentResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'];

		switch ($code) {
			case 0:
				$this->setCode($code)
					->setMessage($message)
					->setTransId($parsedResponse['transId']);

				break;

			case 1400:
				throw new MissingParamException($message, $code);

			default:
				throw new ApiException($message, $code);
		}
	}

	public function toArray()
	{
		return [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'transId' => $this->getTransId(),
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
	 * @return RecurringPaymentResponse
	 */
	public function setCode(int $code): self
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
	 * @return RecurringPaymentResponse
	 */
	public function setMessage(string $message): self
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
	 * @return RecurringPaymentResponse
	 */
	public function setTransId(string $transId): RecurringPaymentResponse
	{
		$this->transId = $transId;
		return $this;
	}
}
