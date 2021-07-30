<?php declare(strict_types = 1);

namespace Comgate\SDK;

class Config
{

	public const URL = 'https://payments.comgate.cz/v1.0/';

	/** @var string */
	private $merchant;

	/** @var string */
	private $secret;

	/** @var bool */
	private $test;

	public function __construct(string $merchant, string $secret, bool $test = false)
	{
		$this->merchant = $merchant;
		$this->secret = $secret;
		$this->test = $test;
	}

	public function getMerchant(): string
	{
		return $this->merchant;
	}

	public function getSecret(): string
	{
		return $this->secret;
	}

	public function isTest(): bool
	{
		return $this->test;
	}

}
