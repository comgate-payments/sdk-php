<?php declare(strict_types = 1);

namespace Comgate\SDK;

use Comgate\SDK\Http\Transport;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Utils;

class Bootstrap
{

	public string $url;

	private string $merchant;

	private string $secret;

	private bool $test = false;

	final private function __construct()
	{
	}

	public static function defaults(): self
	{
		$self = new Bootstrap();
		$self->url = Comgate::URL;

		return $self;
	}

	public static function testing(): self
	{
		$self = self::defaults();
		$self->test = true;

		return $self;
	}

	public function withUrl(string $url): self
	{
		$this->url = $url;

		return $this;
	}

	public function withMerchant(string $merchant): self
	{
		$this->merchant = $merchant;

		return $this;
	}

	public function withSecret(string $secret): self
	{
		$this->secret = $secret;

		return $this;
	}

	public function withTest(bool $test = true): self
	{
		$this->test = $test;

		return $this;
	}

	public function create(): Client
	{
		return new Client(
			new Transport(
				new GuzzleClient([
					'base_uri' => $this->url,
					'headers' => ['User-Agent' => sprintf('ComgateSDK/%s', Utils::defaultUserAgent())],
				]),
				new Comgate($this->merchant, $this->secret, $this->test)
			)
		);
	}

}
