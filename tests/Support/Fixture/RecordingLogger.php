<?php declare(strict_types = 1);

namespace Tests\Support\Fixture;

use Psr\Log\AbstractLogger;

final class RecordingLogger extends AbstractLogger
{
	/** @var array<int, array{level: mixed, message: string}> */
	private $records = [];

	/** @param mixed $level @param mixed $message @param array<string, mixed> $context */
	public function log($level, $message, array $context = []): void
	{
		$this->records[] = ['level' => $level, 'message' => (string) $message];
	}

	/** @return array<int, array{level: mixed, message: string}> */
	public function records(): array
	{
		return $this->records;
	}
}
