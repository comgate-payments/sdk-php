<?php
declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Http\Response;
use Comgate\SDK\Http\Query;
use Comgate\SDK\Exception\ApiException;

class PublicCryptoKeyResponse
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
	 * @var string
	 */
	protected $key;

	/**
	 * @param Response $publicCryptoKeyResponse
	 */
	public function __construct(Response $publicCryptoKeyResponse)
	{
		$parsedResponse = Query::parse($publicCryptoKeyResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'] ?? '';

		switch ($code) {
			case 0:
				$this->setCode($code)
					->setMessage($message)
					->setKey($parsedResponse['key']);
				break;

			default:
					throw new ApiException($message, $code);
		}
	}

	/**
	 * @return array<string, int|string|null>
	 */
	public function toArray(): array
	{
		$output = [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'key' => $this->getKey(),
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
	 * @return PublicCryptoKeyResponse
	 */
	public function setCode(int $code): PublicCryptoKeyResponse
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
	 * @return PublicCryptoKeyResponse
	 */
	public function setMessage(string $message): PublicCryptoKeyResponse
	{
		$this->message = $message;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @param string $key
	 * @return PublicCryptoKeyResponse
	 */
	public function setKey(string $key): PublicCryptoKeyResponse
	{
		$this->key = $key;
		return $this;
	}
}
