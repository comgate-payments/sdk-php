<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

use Comgate\SDK\Entity\Codes\CurrencyCode;

class Payment
{

	/** @var Money */
	protected $price;

	/** @var string */
	protected $currency = CurrencyCode::CZK;

	/** @var string */
	protected $label;

	/** @var string */
	protected $referenceId;

	/** @var string */
	protected $email;

	/** @var string[] */
	protected $allowedMethods = [];

	/** @var string[] */
	protected $excludedMethods = [];

	/** @var bool */
	protected $prepareOnly = true;

	/** @var string|null */
	protected $transactionId = null;

	/** @var string|null */
	protected $country = null;

	/** @var string|null */
	protected $account = null;

	/** @var string|null */
	protected $phone = null;

	/** @var string|null */
	protected $name = null;

	/** @var string|null */
	protected $lang = null;

	/** @var bool|null */
	protected $preauth = null;

	/** @var bool|null */
	protected $initRecurring = null;

	/** @var bool|null */
	protected $verification = null;

	/** @var bool|null */
	protected $embedded = null;

	/** @var bool|null */
	protected $eetReport = null;

	/** @var mixed[]|null */
	protected $eetData = [];

	final private function __construct()
	{
	}

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new static();
	}

	public function withRedirect(): self
	{
		$this->withPrepareOnly(true);

		return $this;
	}

	public function withIframe(): self
	{
		$this->withPrepareOnly(true);
		$this->withEmbedded(true);

		return $this;
	}

	public function getPrice(): Money
	{
		return $this->price;
	}

	/**
	 * @param int|float|Money $price
	 */
	public function withPrice($price): self
	{
		$this->price = Money::of($price);

		return $this;
	}

	public function getCurrency(): string
	{
		return $this->currency;
	}

	public function withCurrency(string $currency): self
	{
		$this->currency = $currency;

		return $this;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function withLabel(string $label): self
	{
		$this->label = $label;

		return $this;
	}

	public function getReferenceId(): string
	{
		return $this->referenceId;
	}

	public function withReferenceId(string $referenceId): self
	{
		$this->referenceId = $referenceId;

		return $this;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function withEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getAllowedMethods(): array
	{
		return $this->allowedMethods;
	}

	/**
	 * @return string[]
	 */
	public function getExcludedMethods(): array
	{
		return $this->excludedMethods;
	}

	public function withMethod(string $method): self
	{
		$this->allowedMethods[] = $method;

		return $this;
	}

	public function withoutMethod(string $method): self
	{
		$this->excludedMethods[] = $method;

		return $this;
	}

	public function getCountry(): ?string
	{
		return $this->country;
	}

	public function withCountry(string $country): self
	{
		$this->country = $country;

		return $this;
	}

	public function getAccount(): ?string
	{
		return $this->account;
	}

	public function withAccount(string $account): self
	{
		$this->account = $account;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function withPhone(string $phone): self
	{
		$this->phone = $phone;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function withName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getLang(): ?string
	{
		return $this->lang;
	}

	public function withLang(string $lang): self
	{
		$this->lang = $lang;

		return $this;
	}

	public function getTransactionId(): ?string
	{
		return $this->transactionId;
	}

	public function withTransactionId(string $transactionId): self
	{
		$this->transactionId = $transactionId;

		return $this;
	}

	public function isPrepareOnly(): bool
	{
		return $this->prepareOnly;
	}

	public function withPrepareOnly(bool $prepareOnly): self
	{
		$this->prepareOnly = $prepareOnly;

		return $this;
	}

	public function isPreauth(): ?bool
	{
		return $this->preauth;
	}

	public function withPreauth(bool $preauth): self
	{
		$this->preauth = $preauth;

		return $this;
	}

	public function isInitRecurring(): ?bool
	{
		return $this->initRecurring;
	}

	public function withInitRecurring(bool $initRecurring): self
	{
		$this->initRecurring = $initRecurring;

		return $this;
	}

	public function isVerification(): ?bool
	{
		return $this->verification;
	}

	public function withVerification(bool $verification): self
	{
		$this->verification = $verification;

		return $this;
	}

	public function isEmbedded(): ?bool
	{
		return $this->embedded;
	}

	public function withEmbedded(bool $embedded): self
	{
		$this->embedded = $embedded;

		return $this;
	}

	public function isEetReport(): ?bool
	{
		return $this->eetReport;
	}

	public function withEetReport(bool $eetReport): self
	{
		$this->eetReport = $eetReport;

		return $this;
	}

	/**
	 * @return mixed[]
	 */
	public function getEetData(): ?array
	{
		return $this->eetData;
	}

	/**
	 * @param mixed[] $eetData
	 */
	public function withEetData(array $eetData): self
	{
		$this->eetData = $eetData;

		return $this;
	}

}
