<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

use Comgate\SDK\Exception\Logical\ParamIsNotSetException;

class Payment
{
	/**
	 * Payment parameters.
	 *
	 * @var array
	 */
	protected $params = [
		'test' => false,
		'prepareOnly' => true,
		'initRecurring' => false,
		'preauth' => false,
		'verification' => false,
		'embedded' => false,
		'allowedMethods' => [],
		'excludedMethods' => [],
		'account' => '',
		'name' => '',
	];

	public function setParam($paramName, $value): self
	{
		$this->params[$paramName] = $value;

		return $this;
	}

	public function getParam($paramName): mixed
	{
		if (isset($this->params[$paramName])) {
			return $this->params[$paramName];
		}

		throw new ParamIsNotSetException("Param {$paramName} is not set.");
	}

	public function getParams(): array
	{
		return $this->params;
	}

	public function setParams(array $params)
	{
		$this->params = $params;

		return $this;
	}

	public function setRedirect(): self
	{
		$this->setPrepareOnly(true);

		return $this;
	}

	public function setIframe(): self
	{
		$this->setPrepareOnly(true);
		$this->setEmbedded(true);

		return $this;
	}

	public function getPrice(): Money
	{
		return $this->getParam('price');
	}

	/**
	 * @param int|float|Money $price
	 */
	public function setPrice($price): self
	{
		$this->setParam('price', Money::of($price));

		return $this;
	}

	public function getCurrency(): string
	{
		return $this->getParam('curr');
	}

	public function setCurrency(string $currency): self
	{
		$this->setParam('curr', $currency);

		return $this;
	}

	public function getLabel(): string
	{
		return $this->getParam('label');
	}

	public function setLabel(string $label): self
	{
		$this->setParam('label', $label);

		return $this;
	}

	public function getReferenceId(): string
	{
		return $this->getParam('refId');
	}

	public function setReferenceId(string $referenceId): self
	{
		$this->setParam('refId', $referenceId);

		return $this;
	}

	public function getEmail(): string
	{
		return $this->getParam('email');
	}

	public function isTest(): bool
	{
		return $this->getParam('test');
	}

	public function setTest(bool $test): self
	{
		$this->setParam('test', $test);

		return $this;
	}

	public function setEmail(string $email): self
	{
		$this->setParam('email', $email);

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getAllowedMethods(): array
	{
		return $this->getParam('allowedMethods');
	}

	/**
	 * @return string[]
	 */
	public function getExcludedMethods(): array
	{
		return $this->getParam('excludedMethods');
	}

	public function setMethods(array $methods): self
	{
		$this->params['allowedMethods'] = $methods;

		return $this;
	}

	public function addMethod(string $method): self
	{
		$this->params['allowedMethods'][] = $method;

		return $this;
	}

	public function setoutMethod(string $method): self
	{
		$this->params['excludedMethods'][] = $method;

		return $this;
	}

	public function getCountry(): ?string
	{
		return $this->getParam('country');
	}

	public function setCountry(string $country): self
	{
		$this->setParam('country', $country);

		return $this;
	}

	public function getAccount(): ?string
	{
		return $this->getParam('account');
	}

	public function setAccount(string $account): self
	{
		$this->setParam('account', $account);

		return $this;
	}

	public function getName(): ?string
	{
		return $this->getParam('name');
	}

	public function setName(string $name): self
	{
		$this->setParam('name', $name);

		return $this;
	}

	public function getLang(): ?string
	{
		return $this->getParam('lang');
	}

	public function setLang(string $lang): self
	{
		$this->setParam('lang', $lang);

		return $this;
	}

	public function getTransactionId(): ?string
	{
		return $this->getParam('transactionId');
	}

	public function setTransactionId(string $transactionId): self
	{
		$this->setParam('transactionId', $transactionId);

		return $this;
	}

	public function isPrepareOnly(): bool
	{
		return $this->getParam('prepareOnly');
	}

	public function setPrepareOnly(bool $prepareOnly): self
	{
		$this->setParam('prepareOnly', $prepareOnly);

		return $this;
	}

	public function isPreauth(): ?bool
	{
		return $this->getParam('preauth');
	}

	public function setPreauth(bool $preauth): self
	{
		$this->setParam('preauth', $preauth);

		return $this;
	}

	public function isInitRecurring(): bool
	{
		return $this->getParam('initRecurring');
	}

	public function setInitRecurring(bool $initRecurring): self
	{
		$this->setParam('initRecurring', $initRecurring);

		return $this;
	}

	public function isVerification(): bool
	{
		return $this->getParam('verification');
	}

	public function setVerification(bool $verification): self
	{
		$this->setParam('verification', $verification);

		return $this;
	}

	public function isEmbedded(): bool
	{
		return $this->getParam('embedded');
	}

	public function setEmbedded(bool $embedded): self
	{
		$this->setParam('embedded', $embedded);

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getPayerId(): ?string
	{
		return $this->getParam('payerId');
	}

	/**
	 * @param string|null $payerId
	 * @return Payment
	 */
	public function setPayerId(?string $payerId): Payment
	{
		$this->setParam('payerId', $payerId);
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getApplePayPayload(): ?string
	{
		return $this->getParam('applePayPayload');
	}

	/**
	 * @param string|null $applePayPayload
	 * @return Payment
	 */
	public function setApplePayPayload(?string $applePayPayload): Payment
	{
		$this->setParam('applePayPayload', $applePayPayload);
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getExpirationTime(): ?string
	{
		return $this->getParam('expirationTime');
	}

	/**
	 * @param string|null $expirationTime
	 * @return Payment
	 */
	public function setExpirationTime(?string $expirationTime): Payment
	{
		$this->setParam('expirationTime', $expirationTime);
		return $this;
	}


	/**
	 * @return string|null
	 */
	public function getInitRecurringId(): ?string
	{
		return $this->getParam('initRecurringId');
	}

	/**
	 * @param string|null $initRecurringId
	 * @return Payment
	 */
	public function setInitRecurringId(?string $initRecurringId): Payment
	{
		$this->setParam('initRecurringId', $initRecurringId);
		return $this;
	}
}
