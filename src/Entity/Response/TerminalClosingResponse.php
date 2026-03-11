<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class TerminalClosingResponse
{
	/** @var int */
	protected $code;

	/** @var string */
	protected $message = '';

	/** @var int */
	protected $batchNumber = 0;

	/** @var array<int, array<string, mixed>> */
	protected $batchData = [];

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
					->setBatchNumber((int) ($data['batchNumber'] ?? 0));

				if (isset($data['batchData']) && is_array($data['batchData'])) {
					$this->setBatchData($data['batchData']);
				}
				break;

			default:
				throw new ApiException($message, $code);
		}
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'batchNumber' => $this->getBatchNumber(),
			'batchData' => $this->getBatchData(),
		];
	}

	public function getCode(): int { return $this->code; }
	public function setCode(int $code): self { $this->code = $code; return $this; }
	public function getMessage(): string { return $this->message; }
	public function setMessage(string $message): self { $this->message = $message; return $this; }
	public function getBatchNumber(): int { return $this->batchNumber; }
	public function setBatchNumber(int $batchNumber): self { $this->batchNumber = $batchNumber; return $this; }

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function getBatchData(): array { return $this->batchData; }

	/**
	 * @param array<int, array<string, mixed>> $batchData
	 * @return self
	 */
	public function setBatchData(array $batchData): self { $this->batchData = $batchData; return $this; }
}
