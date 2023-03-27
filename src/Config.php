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

	/**
	 * @param string $merchant
	 * @return Config
	 */
	public function setMerchant(string $merchant): Config
	{
		$this->merchant = $merchant;
		return $this;
	}

	/**
	 * @param string $secret
	 * @return Config
	 */
	public function setSecret(string $secret): Config
	{
		$this->secret = $secret;
		return $this;
	}
}
