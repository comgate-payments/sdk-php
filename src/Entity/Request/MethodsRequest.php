<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Codes\TypeCode;

class MethodsRequest implements IRequest
{
	/**
	 * @var string
	 */
	private $type = TypeCode::TYPE_JSON;
	/**
	 * @var string|null
	 */
	private $lang = null;
	/**
	 * @var string|null
	 */
	private $currency = null;
	/**
	 * @var string|null
	 */
	private $country = null;
	/**
	 * @var string|null
	 */
	private $price = null;
	/**
	 * @var bool|null
	 */
	private $initRecurring = null;
	/**
	 * @var bool|null
	 */
	private $verification = null;
	/**
	 * @var bool|null
	 */
	private $preauth = null;
	/**
	 * @var string|null
	 */
	private $expirationTime = null;
	/**
	 * @var bool|null
	 */
	private $embedded = null;
	/**
	 * @var string|null
	 */
	private $userAgent = null;
	/**
	 * @var bool|null
	 */
	private $chargeUnregulatedCardFees = null;
	/**
	 * @var bool|null
	 */
	private $enableApplePayGooglePay = null;

	public function getUrn(): string
	{
		return 'methods';
	}

	/**
	 * @return array<string, string|int>
	 */
	public function toArray(): array
	{
		$requestArray = [
			'type' => $this->getType(),
		];

		if(!is_null($this->getLang())){
			$requestArray['lang'] = $this->getLang();
		}

		if(!is_null($this->getCurr())){
			$requestArray['curr'] = $this->getCurr();
		}

		if(!is_null($this->getCountry())){
			$requestArray['country'] = $this->getCountry();
		}

		if(!is_null($this->getPrice())) {
			$requestArray['price'] = $this->getPrice();
		}

		if(!is_null($this->getInitRecurring())){
			$requestArray['initRecurring'] = $this->getInitRecurring();
		}
		if(!is_null($this->getVerification())){
			$requestArray['verification'] = $this->getVerification();
		}
		if(!is_null($this->getPreauth())){
			$requestArray['preauth'] = $this->getPreauth();
		}
		if(!is_null($this->getExpirationTime())){
			$requestArray['expirationTime'] = $this->getExpirationTime();
		}
		if(!is_null($this->getEmbedded())){
			$requestArray['embedded'] = $this->getEmbedded();
		}
		if(!is_null($this->getUserAgent())){
			$requestArray['userAgent'] = $this->getUserAgent();
		}
		if(!is_null($this->getChargeUnregulatedCardFees())){
			$requestArray['chargeUnregulatedCardFees'] = $this->getChargeUnregulatedCardFees();
		}
		if(!is_null($this->getEnableApplePayGooglePay())){
			$requestArray['enableApplePayGooglePay'] = $this->getEnableApplePayGooglePay();
		}

		return $requestArray;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return MethodsRequest
	 */
	public function setType(string $type): MethodsRequest
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getLang(): ?string
	{
		return $this->lang;
	}

	/**
	 * @param string|null $lang
	 * @return MethodsRequest
	 */
	public function setLang(?string $lang): MethodsRequest
	{
		$this->lang = $lang;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCurr(): ?string
	{
		return $this->currency;
	}

	/**
	 * @param string|null $curr
	 * @return MethodsRequest
	 */
	public function setCurr(?string $curr): MethodsRequest
	{
		$this->currency = $curr;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCountry(): ?string
	{
		return $this->country;
	}

	/**
	 * @param string|null $country
	 * @return MethodsRequest
	 */
	public function setCountry(?string $country): MethodsRequest
	{
		$this->country = $country;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getPrice(): ?string
	{
		return $this->price;
	}

	/**
	 * @param string|null $price
	 * @return void
	 */
	public function setPrice(?string $price): void
	{
		$this->price = $price;
	}

	/**
	 * @return bool|null
	 */
	public function getInitRecurring(): ?bool
	{
		return $this->initRecurring;
	}

	/**
	 * @param bool|null $initRecurring
	 * @return void
	 */
	public function setInitRecurring(?bool $initRecurring): void
	{
		$this->initRecurring = $initRecurring;
	}

	/**
	 * @return bool|null
	 */
	public function getVerification(): ?bool
	{
		return $this->verification;
	}

	/**
	 * @param bool|null $verification
	 * @return void
	 */
	public function setVerification(?bool $verification): void
	{
		$this->verification = $verification;
	}

	/**
	 * @return bool|null
	 */
	public function getPreauth(): ?bool
	{
		return $this->preauth;
	}

	/**
	 * @param bool|null $preauth
	 * @return void
	 */
	public function setPreauth(?bool $preauth): void
	{
		$this->preauth = $preauth;
	}

	/**
	 * @return string|null
	 */
	public function getExpirationTime(): ?string
	{
		return $this->expirationTime;
	}

	/**
	 * @param string|null $expirationTime
	 * @return void
	 */
	public function setExpirationTime(?string $expirationTime): void
	{
		$this->expirationTime = $expirationTime;
	}

	/**
	 * @return bool|null
	 */
	public function getEmbedded(): ?bool
	{
		return $this->embedded;
	}

	/**
	 * @param bool|null $embedded
	 * @return void
	 */
	public function setEmbedded(?bool $embedded): void
	{
		$this->embedded = $embedded;
	}

	/**
	 * @return string|null
	 */
	public function getUserAgent(): ?string
	{
		return $this->userAgent;
	}

	/**
	 * @param string|null $userAgent
	 * @return void
	 */
	public function setUserAgent(?string $userAgent): void
	{
		$this->userAgent = $userAgent;
	}

	/**
	 * @return bool|null
	 */
	public function getChargeUnregulatedCardFees(): ?bool
	{
		return $this->chargeUnregulatedCardFees;
	}

	/**
	 * @param bool|null $chargeUnregulatedCardFees
	 * @return void
	 */
	public function setChargeUnregulatedCardFees(?bool $chargeUnregulatedCardFees): void
	{
		$this->chargeUnregulatedCardFees = $chargeUnregulatedCardFees;
	}

	/**
	 * @return bool|null
	 */
	public function getEnableApplePayGooglePay(): ?bool
	{
		return $this->enableApplePayGooglePay;
	}

	/**
	 * @param bool|null $enableApplePayGooglePay
	 * @return void
	 */
	public function setEnableApplePayGooglePay(?bool $enableApplePayGooglePay): void
	{
		$this->enableApplePayGooglePay = $enableApplePayGooglePay;
	}
}
