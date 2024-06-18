<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

use Comgate\SDK\Exception\Logical\ParamIsNotSetException;
use Exception;

class Payment
{
	/**
	 * Payment parameters.
	 *
	 * @var array<string, bool|int|string|Money|array<int, string>>
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
		// ... other params whithout default value
		// 'fullName' => '',
		// 'billingAddrCity' => '',
		// 'billingAddrStreet' => '',
		// 'billingAddrPostalCode' => '',
		// 'billingAddrCountry' => '',
		// 'delivery' => '',
		// 'homeDeliveryCity' => '',
		// 'homeDeliveryStreet' => '',
		// 'homeDeliveryPostalCode' => '',
		// 'homeDeliveryCountry' => '',
		// 'category' => '',
	];

	/**
	 * @param string $paramName
	 * @param mixed $value
	 * @return $this
	 */
	public function setParam(string $paramName, $value): self
	{
		$this->params[$paramName] = $value;

		return $this;
	}

	/**
	 * @param string $paramName
	 * @return bool|int|string|Money|array<int, string>
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
	 * @return array<string, bool|int|string|Money|array<int, string>>
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @param array<string, bool|int|string|Money> $params
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
		$param = $this->getParam('price');
		if (!($param instanceof Money)) {
			throw new Exception("The Money value is not instance of Money");
		}

		return $param;
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
		return (string) $this->getParamWithoutMoney('curr');
	}

	public function setCurrency(string $currency): self
	{
		$this->setParam('curr', $currency);

		return $this;
	}

	public function getLabel(): string
	{
		return (string) $this->getParamWithoutMoney('label');
	}

	public function setLabel(string $label): self
	{
		$this->setParam('label', $label);

		return $this;
	}

	public function getReferenceId(): string
	{
		return (string) $this->getParamWithoutMoney('refId');
	}

	public function setReferenceId(string $referenceId): self
	{
		$this->setParam('refId', $referenceId);

		return $this;
	}

	public function getEmail(): string
	{
		return (string) $this->getParamWithoutMoney('email');
	}

	public function isTest(): bool
	{
		return (bool)$this->getParam('test');
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
	 * @return array<int, string>
	 */
	public function getAllowedMethods(): array
	{
		return (array)$this->getParam('allowedMethods');
	}

	/**
	 * @return array<int, string>
	 */
	public function getExcludedMethods(): array
	{
		return (array)$this->getParam('excludedMethods');
	}

	/**
	 *
	 * @param array<int, string> $methods
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
		if (!isset($this->params['allowedMethods']) || !is_array($this->params['allowedMethods'])) {
			$this->params['allowedMethods'] = [];
		}

		$this->params['allowedMethods'][] = $method;

		return $this;
	}

	public function setoutMethod(string $method): self
	{
		if (!isset($this->params['excludedMethods']) || !is_array($this->params['excludedMethods'])) {
			$this->params['excludedMethods'] = [];
		}

		$this->params['excludedMethods'][] = $method;

		return $this;
	}

	public function getCountry(): ?string
	{
		return (string) $this->getParamWithoutMoney('country');
	}

	public function setCountry(string $country): self
	{
		$this->setParam('country', $country);

		return $this;
	}

	public function getAccount(): ?string
	{
		return (string) $this->getParamWithoutMoney('account');
	}

	public function setAccount(string $account): self
	{
		$this->setParam('account', $account);

		return $this;
	}

	public function getName(): ?string
	{
		return (string) $this->getParamWithoutMoney('name');
	}

	public function setName(string $name): self
	{
		$this->setParam('name', $name);

		return $this;
	}

	public function getLang(): ?string
	{
		return (string) $this->getParamWithoutMoney('lang');
	}

	public function setLang(string $lang): self
	{
		$this->setParam('lang', $lang);

		return $this;
	}

	public function getTransactionId(): ?string
	{
		return (string) $this->getParamWithoutMoney('transactionId');
	}

	public function setTransactionId(string $transactionId): self
	{
		$this->setParam('transactionId', $transactionId);

		return $this;
	}

	public function isPrepareOnly(): bool
	{
		return (bool)$this->getParam('prepareOnly');
	}

	public function setPrepareOnly(bool $prepareOnly): self
	{
		$this->setParam('prepareOnly', $prepareOnly);

		return $this;
	}

	public function isPreauth(): bool
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
		return (bool)$this->getParam('initRecurring');
	}

	public function setInitRecurring(bool $initRecurring): self
	{
		$this->setParam('initRecurring', $initRecurring);

		return $this;
	}

	public function isVerification(): bool
	{
		return (bool)$this->getParam('verification');
	}

	public function setVerification(bool $verification): self
	{
		$this->setParam('verification', $verification);

		return $this;
	}

	public function isEmbedded(): bool
	{
		return (bool)$this->getParam('embedded');
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
		return (string) $this->getParamWithoutMoney('payerId');
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
		return (string) $this->getParamWithoutMoney('applePayPayload');
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
		return (string) $this->getParamWithoutMoney('expirationTime');
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
		return (string) $this->getParamWithoutMoney('initRecurringId');
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
		return (bool)$this->getParam('dynamicExpiration');
	}

	public function setDynamicExpiration(bool $dynamicExpiration): self
	{
		$this->setParam('dynamicExpiration', $dynamicExpiration);

		return $this;
	}

	public function getFullName(): ?string
	{
		return (string) $this->getParamWithoutMoney('fullName');
	}

	public function setFullName(string $fullName): self
	{
		$this->setParam('fullName', $fullName);

		return $this;
	}

	public function getBillingAddrCity(): ?string
	{
		return (string) $this->getParamWithoutMoney('billingAddrCity');
	}

	public function setBillingAddrCity(string $billingAddrCity): self
	{
		$this->setParam('billingAddrCity', $billingAddrCity);

		return $this;
	}

	public function getBillingAddrStreet(): ?string
	{
		return (string) $this->getParamWithoutMoney('billingAddrStreet');
	}

	public function setBillingAddrStreet(string $billingAddrStreet): self
	{
		$this->setParam('billingAddrStreet', $billingAddrStreet);

		return $this;
	}

	public function getBillingAddrPostalCode(): ?string
	{
		return (string) $this->getParamWithoutMoney('billingAddrPostalCode');
	}

	public function setBillingAddrPostalCode(string $billingAddrPostalCode): self
	{
		$this->setParam('billingAddrPostalCode', $billingAddrPostalCode);

		return $this;
	}

	public function getBillingAddrCountry(): ?string
	{
		return (string) $this->getParamWithoutMoney('billingAddrCountry');
	}

	public function setBillingAddrCountry(string $billingAddrCountry): self
	{
		$this->setParam('billingAddrCountry', $billingAddrCountry);

		return $this;
	}

	public function getDelivery(): ?string
	{
		return (string) $this->getParamWithoutMoney('delivery');
	}

	public function setDelivery(string $delivery): self
	{
		$this->setParam('delivery', $delivery);

		return $this;
	}

	public function getHomeDeliveryCity(): ?string
	{
		return (string) $this->getParamWithoutMoney('homeDeliveryCity');
	}

	public function setHomeDeliveryCity(string $homeDeliveryCity): self
	{
		$this->setParam('homeDeliveryCity', $homeDeliveryCity);

		return $this;
	}

	public function getHomeDeliveryStreet(): ?string
	{
		return (string) $this->getParamWithoutMoney('homeDeliveryStreet');
	}

	public function setHomeDeliveryStreet(string $homeDeliveryStreet): self
	{
		$this->setParam('homeDeliveryStreet', $homeDeliveryStreet);

		return $this;
	}

	public function getHomeDeliveryPostalCode(): ?string
	{
		return (string) $this->getParamWithoutMoney('homeDeliveryPostalCode');
	}

	public function setHomeDeliveryPostalCode(string $homeDeliveryPostalCode): self
	{
		$this->setParam('homeDeliveryPostalCode', $homeDeliveryPostalCode);

		return $this;
	}

	public function getHomeDeliveryCountry(): ?string
	{
		return (string) $this->getParamWithoutMoney('homeDeliveryCountry');
	}

	public function setHomeDeliveryCountry(string $homeDeliveryCountry): self
	{
		$this->setParam('homeDeliveryCountry', $homeDeliveryCountry);

		return $this;
	}

	public function getCategory(): ?string
	{
		return (string) $this->getParamWithoutMoney('category');
	}

	public function setCategory(string $category): self
	{
		$this->setParam('category', $category);

		return $this;
	}

	/**
	 * @param string $attributeName
	 * @return bool|int|string
	 * @throws Exception
	 */
	private function getParamWithoutMoney(string $attributeName)
	{
		$param = $this->getParam($attributeName);
		if ($param instanceof Money) {
			throw new Exception("There is a Money value in {$attributeName} attribute");
		}

		return $param;
	}
}
