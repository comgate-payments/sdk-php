<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

class MotoPayment extends Payment
{
	public function __construct()
	{
		$this->params['encryptedCardNumber'] = '';
		$this->params['encryptedCardExpiration'] = '';
		$this->params['encryptedCardCvv'] = '';
	}

	public function getEncryptedCardNumber(): ?string
	{
		return (string) $this->getParamWithoutMoney('encryptedCardNumber');
	}

	public function setEncryptedCardNumber(string $encryptedCardNumber): self
	{
		$this->setParam('encryptedCardNumber', $encryptedCardNumber);

		return $this;
	}

	public function getEncryptedCardExpiration(): ?string
	{
		return (string) $this->getParamWithoutMoney('encryptedCardExpiration');
	}

	public function setEncryptedCardExpiration(string $encryptedCardExpiration): self
	{
		$this->setParam('encryptedCardExpiration', $encryptedCardExpiration);

		return $this;
	}

	public function getEncryptedCardCvv(): ?string
	{
		return (string) $this->getParamWithoutMoney('encryptedCardCvv');
	}

	public function setEncryptedCardCvv(string $encryptedCardCvv): self
	{
		$this->setParam('encryptedCardCvv', $encryptedCardCvv);

		return $this;
	}
}
