<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Exception\LogicalException;

class Payment extends Entity
{

	protected Money $price;

	protected string $currency = CurrencyCode::CZK;

	protected string $label;

	protected string $referenceId;

	protected string $email;

	protected string $method = PaymentMethodCode::ALL;

	protected bool $prepareOnly = true;

	protected ?string $country = null;

	protected ?string $account = null;

	protected ?string $phone = null;

	protected ?string $name = null;

	protected ?string $lang = null;

	protected ?bool $preauth = null;

	protected ?bool $initRecurring = null;

	protected ?bool $verification = null;

	protected ?bool $embedded = null;

	protected ?bool $eetReport = null;

	/** @var mixed[] */
	protected ?array $eetData = [];

	protected function __construct()
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

	public function getMethod(): string
	{
		return $this->method;
	}

	public function withMethod(string $method): self
	{
		$this->method = $method;

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

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		// Required

		$output = [
			'price' => $this->price->get(), // in cents 10.25 => 1025
			'curr' => $this->currency,
			'label' => $this->label,
			'refId' => $this->referenceId,
			'method' => $this->method,
			'email' => $this->email,
			'prepareOnly' => $this->prepareOnly ? 'true' : 'false',
		];

		// Optional

		if ($this->phone !== null) {
			$output['phone'] = $this->phone;
		}

		if ($this->name !== null) {
			$output['name'] = $this->name;
		}

		if ($this->country !== null) {
			$output['country'] = $this->country;
		}

		if ($this->account !== null) {
			$output['account'] = $this->account;
		}

		if ($this->lang !== null) {
			$output['lang'] = $this->lang;
		}

		if ($this->preauth !== null) {
			$output['preauth'] = $this->preauth ? 'true' : 'false';
		}

		if ($this->initRecurring !== null) {
			if ($this->prepareOnly !== true) {
				throw new LogicalException('Field initRecurring requires prepareOnly=true');
			}

			$output['initRecurring'] = $this->initRecurring ? 'true' : 'false';
		}

		if ($this->verification !== null) {
			$output['initRecurring'] = $this->verification ? 'true' : 'false';
		}

		if ($this->embedded !== null) {
			$output['embedded'] = $this->embedded ? 'true' : 'false';
		}

		if ($this->eetReport !== null) {
			$output['eetReport'] = $this->eetReport ? 'true' : 'false';
		}

		if ($this->eetData !== []) {
			$output['eetData'] = $this->eetData;
		}

		return $output;
	}

}
