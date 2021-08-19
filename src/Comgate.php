<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Http\Transport;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Utils;
use Psr\Log\LoggerInterface;

class Comgate
{

	/** @var string */
	public $url;

	/** @var string */
	private $merchant;

	/** @var string */
	private $secret;

	/** @var bool */
	private $test = false;

	/** @var callable[] */
	private $middlewares = [];

	/** @var LoggerInterface|null */
	private $logger = null;

	final private function __construct()
	{
	}

	/**
	 * @return static
	 */
	public static function defaults(): self
	{
		$self = new static();
		$self->url = Config::URL;

		return $self;
	}

	/**
	 * @return static
	 */
	public static function testing(): self
	{
		$self = self::defaults();
		$self->test = true;

		return $self;
	}

	/**
	 * @return static
	 */
	public function withUrl(string $url): self
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withMerchant(string $merchant): self
	{
		$this->merchant = $merchant;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withSecret(string $secret): self
	{
		$this->secret = $secret;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withTest(bool $test = true): self
	{
		$this->test = $test;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withMiddleware(callable $middleware): self
	{
		$this->middlewares[] = $middleware;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withLogger(LoggerInterface $logger): self
	{
		$this->logger = $logger;

		return $this;
	}

	public function createClient(): Client
	{
		return new Client($this->createTransport());
	}

	protected function createConfig(): Config
	{
		return new Config($this->merchant, $this->secret, $this->test);
	}

	protected function createGuzzle(?callable $handler = null): ClientInterface
	{
		$stack = HandlerStack::create($handler);

		foreach ($this->middlewares as $middleware) {
			$stack->push($middleware);
		}

		if ($this->logger !== null) {
			$stack->push(Middleware::log($this->logger, new MessageFormatter(MessageFormatter::DEBUG)));
		}

		return new GuzzleClient([
			'handler' => $stack,
			'base_uri' => $this->url,
			'headers' => ['User-Agent' => sprintf('ComgateSDK/v1/%s', Utils::defaultUserAgent())],
		]);
	}

	protected function createTransport(): ITransport
	{
		return new Transport($this->createGuzzle(), $this->createConfig());
	}

}
