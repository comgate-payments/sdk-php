<?php declare(strict_types = 1);

namespace Comgate\SDK;

class Comgate
{

	public const URL = 'https://payments.comgate.cz/v1.0/';

	private string $merchant;

	private string $secret;

	private bool $test;

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
