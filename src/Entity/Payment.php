<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

use Comgate\SDK\Exception\Logical\ParamIsNotSetException;

class Payment
{
	/**
	 * Payment parameters.
	 *
	 * @var array<string, int|string>
	 */
	protected $params = [
		'test' => false,
		'prepareOnly' => true,
		'initRecurring' => false,
		'preauth' => false,
		'verification' => false,
		'embedded' => false,
		'dynamicExpiration' => false,
		'allowedMethods' => [],
		'excludedMethods' => [],
		'account' => '',
		'name' => '',
        'fullName' => '',
		'billingAddrCity' => '',
		'billingAddrStreet' => '',
		'billingAddrPostalCode' => '',
		'billingAddrCountry' => '',
		'delivery' => '',
		'homeDeliveryCity' => '',
		'homeDeliveryStreet' => '',
		'homeDeliveryPostalCode' => '',
		'homeDeliveryCountry' => '',
		'category' => '',
		// ... other params whithout default value
	];

        /**
         *
         * @param string $paramName
         * @param mixed $value
         * @return self
         */
	public function setParam($paramName, $value): self
	{
		$this->params[$paramName] = $value;

		return $this;
	}

        /**
         *
         * @param string $paramName
         * @return string | Money
         * @throws ParamIsNotSetException
         */
	public function getParam($paramName)
	{
		if (isset($this->params[$paramName])) {
			return $this->params[$paramName];
		}

		throw new ParamIsNotSetException("Param {$paramName} is not set.");
	}

        /**
         *
         * @return array<string, int|string>
         */
	public function getParams(): array
	{
		return $this->params;
	}

        /**
         *
         * @param array<string, int|string> $params
         * @return self
         */
	public function setParams(array $params): self
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

	/**
	 *
	 * @return Money
	 */
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
		return(bool) $this->getParam('test');
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
	 * @return array<string>
	 */
	public function getAllowedMethods(): array
	{
		return(array) $this->getParam('allowedMethods');
	}

	/**
	 * @return array<string>
	 */
	public function getExcludedMethods(): array
	{
		return(array) $this->getParam('excludedMethods');
	}

        /**
         *
         * @param array<string, int|string> $methods
         * @return self
         */
	public function setMethods(array $methods): self
	{
		$this->params['allowedMethods'] = $methods;

		return $this;
	}

        /**
         *
         * @param string $method
         * @return self
         */
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
		return(bool) $this->getParam('prepareOnly');
	}

	public function setPrepareOnly(bool $prepareOnly): self
	{
		$this->setParam('prepareOnly', $prepareOnly);

		return $this;
	}

	public function isPreauth(): ?bool
	{
		return (bool)$this->getParam('preauth');
	}

	public function setPreauth(bool $preauth): self
	{
		$this->setParam('preauth', $preauth);

		return $this;
	}

	public function isInitRecurring(): bool
	{
		return(bool) $this->getParam('initRecurring');
	}

	public function setInitRecurring(bool $initRecurring): self
	{
		$this->setParam('initRecurring', $initRecurring);

		return $this;
	}

	public function isVerification(): bool
	{
		return(bool) $this->getParam('verification');
	}

	public function setVerification(bool $verification): self
	{
		$this->setParam('verification', $verification);

		return $this;
	}

	public function isEmbedded(): bool
	{
		return(bool) $this->getParam('embedded');
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

	public function isDynamicExpiration(): bool
	{
		return(bool) $this->getParam('dynamicExpiration');
	}

	public function setDynamicExpiration(bool $dynamicExpiration): self
	{
		$this->setParam('dynamicExpiration', $dynamicExpiration);

		return $this;
	}

	public function getFullName(): ?string
	{
		return $this->getParam('fullName');
	}

	public function setFullName(string $fullName): self
	{
		$this->setParam('fullName', $fullName);

		return $this;
	}

	public function getBillingAddrCity(): ?string
	{
		return $this->getParam('billingAddrCity');
	}

	public function setBillingAddrCity(string $billingAddrCity): self
	{
		$this->setParam('billingAddrCity', $billingAddrCity);

		return $this;
	}

	public function getBillingAddrStreet(): ?string
	{
		return $this->getParam('billingAddrStreet');
	}

	public function setBillingAddrStreet(string $billingAddrStreet): self
	{
		$this->setParam('billingAddrStreet', $billingAddrStreet);

		return $this;
	}

	public function getBillingAddrPostalCode(): ?string
	{
		return $this->getParam('billingAddrPostalCode');
	}

	public function setBillingAddrPostalCode(string $billingAddrPostalCode): self
	{
		$this->setParam('billingAddrPostalCode', $billingAddrPostalCode);

		return $this;
	}

	public function getBillingAddrCountry(): ?string
	{
		return $this->getParam('billingAddrCountry');
	}

	public function setBillingAddrCountry(string $billingAddrCountry): self
	{
		$this->setParam('billingAddrCountry', $billingAddrCountry);

		return $this;
	}

	public function getDelivery(): ?string
	{
		return $this->getParam('delivery');
	}

	public function setDelivery(string $delivery): self
	{
		$this->setParam('delivery', $delivery);

		return $this;
	}

	public function getHomeDeliveryCity(): ?string
	{
		return $this->getParam('homeDeliveryCity');
	}

	public function setHomeDeliveryCity(string $homeDeliveryCity): self
	{
		$this->setParam('homeDeliveryCity', $homeDeliveryCity);

		return $this;
	}

	public function getHomeDeliveryStreet(): ?string
	{
		return $this->getParam('homeDeliveryStreet');
	}

	public function setHomeDeliveryStreet(string $homeDeliveryStreet): self
	{
		$this->setParam('homeDeliveryStreet', $homeDeliveryStreet);

		return $this;
	}

	public function getHomeDeliveryPostalCode(): ?string
	{
		return $this->getParam('homeDeliveryPostalCode');
	}

	public function setHomeDeliveryPostalCode(string $homeDeliveryPostalCode): self
	{
		$this->setParam('homeDeliveryPostalCode', $homeDeliveryPostalCode);

		return $this;
	}

	public function getHomeDeliveryCountry(): ?string
	{
		return $this->getParam('homeDeliveryCountry');
	}

	public function setHomeDeliveryCountry(string $homeDeliveryCountry): self
	{
		$this->setParam('homeDeliveryCountry', $homeDeliveryCountry);

		return $this;
	}

	public function getCategory(): ?string
	{
		return $this->getParam('category');
	}

	public function setCategory(string $category): self
	{
		$this->setParam('category', $category);

		return $this;
	}
}
