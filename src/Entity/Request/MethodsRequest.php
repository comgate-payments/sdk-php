<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Request;

use Comgate\SDK\Entity\Codes\TypeCode;

class MethodsRequest implements IRequest
{
	private string $type = TypeCode::TYPE_JSON;
	private ?string $lang = null;
	private ?string $currency = null;
	private ?string $country = null;

	public function getUrn(): string
	{
		return 'methods';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$requestArray = [
			'type' => $this->getType(),
		];

		if(!is_null($this->getLang())){
			$requestArray['lang'] = $this->getLang();
		}

		if(!is_null($this->getCurrency())){
			$requestArray['curr'] = $this->getCurrency();
		}

		if(!is_null($this->getCountry())){
			$requestArray['country'] = $this->getCountry();
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
	public function getCurrency(): ?string
	{
		return $this->currency;
	}

	/**
	 * @param string|null $currency
	 * @return MethodsRequest
	 */
	public function setCurrency(?string $currency): MethodsRequest
	{
		$this->currency = $currency;
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
}
