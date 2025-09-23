<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\Api\PreauthException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Comgate\SDK\Http\Query;

class SimulationResponse
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
	 * @param Response $simulationResponse
	 * @throws ApiException
	 */
	public function __construct(Response $simulationResponse)
	{
		$parsedResponse = Query::parse($simulationResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'];

		switch ($code) {
			case 0:
				$this->setCode($code)
					->setMessage($message);

				break;

			default:
				throw new ApiException($message, $code);
		}
	}

        /**
         *
         * @return array<string, int|string>
         */
	public function toArray(): array
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
	 * @return SimulationResponse
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
	 * @return SimulationResponse
	 */
	public function setMessage(string $message): self
	{
		$this->message = $message;
		return $this;
	}
}
