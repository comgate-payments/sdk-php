<?php declare(strict_types = 1);

namespace Comgate\SDK;

class Config
{

	public const URL = 'https://payments.comgate.cz/v1.0/';

	/** @var string */
	private $merchant;

	/** @var string */
	private $secret;

	public function __construct(string $merchant, string $secret)
	{
		$this->merchant = $merchant;
		$this->secret = $secret;
	}

	public function getMerchant(): string
	{
		return $this->merchant;
	}

	public function getSecret(): string
	{
		return $this->secret;
	}

}
