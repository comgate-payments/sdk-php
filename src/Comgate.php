<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Http\Transport;
use Psr\Log\LoggerInterface;

class Comgate
{

	/** @var string */
	public $url;

	/** @var string */
	private $merchant;

	/** @var string */
	private $secret;

	/**
	 * @var array<string, array<string, mixed>>
	 */
	private $options = [];

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
	public function setUrl(string $url): self
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setMerchant(string $merchant): self
	{
		$this->merchant = $merchant;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setSecret(string $secret): self
	{
		$this->secret = $secret;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setLogger(LoggerInterface $logger): self
	{
		$this->logger = $logger;

		return $this;
	}

	/**
	 * @return Client
	 */
	public function createClient(): Client
	{
		return new Client($this->createTransport());
	}

	/**
	 * @return Config
	 */
	protected function createConfig(): Config
	{
		return new Config($this->merchant, $this->secret, $this->url, $this->options);
	}

	/**
	 * @return ITransport
	 */
	protected function createTransport(): ITransport
	{
		return new Transport($this->createConfig(), $this->logger);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param array<string, array<string, mixed>> $options
	 * @return $this
	 */
	public function setOptions(array $options): self
	{
		$this->options = $options;
		return $this;
	}
}
