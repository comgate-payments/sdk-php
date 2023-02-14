<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\Api\PreauthException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use GuzzleHttp\Psr7\Query;

class PreauthCancelResponse
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

			case 1401:
				throw new PreauthException($message, $code);
				break;

			default:
				throw new ApiException($message, $code);
				break;
		}

		return $this;
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
	 * @return PreauthCancelResponse
	 */
	public function setCode(int $code): PreauthCancelResponse
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
	 * @return PreauthCancelResponse
	 */
	public function setMessage(string $message): PreauthCancelResponse
	{
		$this->message = $message;
		return $this;
	}
}
