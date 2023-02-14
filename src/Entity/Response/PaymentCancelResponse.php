<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use GuzzleHttp\Psr7\Query;

class PaymentCancelResponse
{

	protected int $code;

	protected string $message;

	public function __construct(Response $cancelPreauthResponse)
	{
		$parsedResponse = Query::parse($cancelPreauthResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'];

		switch ($code) {
			case 0:
				$this->setCode($code)
					->setMessage($message);

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
	 * @return PaymentCreateResponse
	 */
	public function setMessage(string $message): self
	{
		$this->message = $message;
		return $this;
	}
}
