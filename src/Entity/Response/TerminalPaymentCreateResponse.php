<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class TerminalPaymentCreateResponse
{
	/** @var int */
	protected $code;

	/** @var string */
	protected $message = '';

	/** @var string */
	protected $transId = '';

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
					->setTransId($data['transId'] ?? '');
				break;

			default:
				throw new ApiException($message, $code);
		}
	}

	/**
	 * @return array<string, int|string>
	 */
	public function toArray(): array
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
	 * @return self
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
	 * @return self
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
	 * @return self
	 */
	public function setTransId(string $transId): self
	{
		$this->transId = $transId;
		return $this;
	}
}
