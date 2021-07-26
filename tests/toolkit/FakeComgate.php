<?php declare(strict_types = 1);

namespace Tests\Toolkit;

use Comgate\SDK\Comgate;
use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Http\Transport;
use GuzzleHttp\Handler\MockHandler;

final class FakeComgate extends Comgate
{

	private MockHandler $handler;

	public static function tests(): FakeComgate
	{
		$self = self::testing();
		$self->withMerchant('123456');
		$self->withSecret('foobarbaz');

		return $self;
	}

	/**
	 * @param array<int, mixed> $queue
	 * @return static
	 */
	public function fakeHttp(array $queue): self
	{
		$this->handler = new MockHandler($queue);

		return $this;
	}

	protected function createTransport(): ITransport
	{
		return new Transport($this->createGuzzle($this->handler), $this->createConfig());
	}

}
