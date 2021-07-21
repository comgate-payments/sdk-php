<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

class PaymentNotification extends Entity
{

	protected ?string $merchant;

	protected ?bool $test;

	protected ?Money $price;

	protected ?string $currency;

	protected ?string $label;

	protected ?string $referenceId;

	protected ?string $email;

	protected ?string $transactionId;

	protected ?string $status;

	protected ?string $fee;

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

	/**
	 * @return static
	 */
	public static function createFromGlobals(): self
	{
		return self::createFrom($_POST);
	}

	/**
	 * @param mixed[] $data
	 * @return static
	 */
	public static function createFrom(array $data): self
	{
		$self = new static();
		$self->merchant = $data['merchant'] ?? null;
		$self->test = filter_var($data['test'] ?? null, FILTER_VALIDATE_BOOLEAN);
		$self->price = isset($data['price']) ? Money::ofCents((int) $data['price']) : null;
		$self->currency = $data['curr'] ?? null;
		$self->label = $data['label'] ?? null;
		$self->referenceId = $data['refId'] ?? null;
		$self->email = $data['email'] ?? null;
		$self->transactionId = $data['transId'] ?? null;
		$self->status = $data['status'] ?? null;
		$self->fee = $data['fee'] ?? null;

		return $self;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'merchant' => $this->merchant,
			'test' => $this->test,
			'price' => $this->price !== null ? $this->price->get() : null,
			'currency' => $this->currency,
			'label' => $this->label,
			'referenceId' => $this->referenceId,
			'email' => $this->email,
			'transactionId' => $this->transactionId,
			'status' => $this->status,
			'free' => $this->fee,
		];
	}

	/**
	 * @return string|null
	 */
	public function getTransactionId(): ?string
	{
		return $this->transactionId;
	}

	/**
	 * @param string|null $transactionId
	 * @return PaymentNotification
	 */
	public function setTransactionId(?string $transactionId): PaymentNotification
	{
		$this->transactionId = $transactionId;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getMerchant(): ?string
	{
		return $this->merchant;
	}

	/**
	 * @param string|null $merchant
	 * @return PaymentNotification
	 */
	public function setMerchant(?string $merchant): PaymentNotification
	{
		$this->merchant = $merchant;
		return $this;
	}

	/**
	 * @return bool|null
	 */
	public function getTest(): ?bool
	{
		return $this->test;
	}

	/**
	 * @param bool|null $test
	 * @return PaymentNotification
	 */
	public function setTest(?bool $test): PaymentNotification
	{
		$this->test = $test;
		return $this;
	}

	/**
	 * @return Money|null
	 */
	public function getPrice(): ?Money
	{
		return $this->price;
	}

	/**
	 * @param Money|null $price
	 * @return PaymentNotification
	 */
	public function setPrice(?Money $price): PaymentNotification
	{
		$this->price = $price;
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
	 * @return PaymentNotification
	 */
	public function setCurrency(?string $currency): PaymentNotification
	{
		$this->currency = $currency;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getLabel(): ?string
	{
		return $this->label;
	}

	/**
	 * @param string|null $label
	 * @return PaymentNotification
	 */
	public function setLabel(?string $label): PaymentNotification
	{
		$this->label = $label;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getReferenceId(): ?string
	{
		return $this->referenceId;
	}

	/**
	 * @param string|null $referenceId
	 * @return PaymentNotification
	 */
	public function setReferenceId(?string $referenceId): PaymentNotification
	{
		$this->referenceId = $referenceId;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * @param string|null $email
	 * @return PaymentNotification
	 */
	public function setEmail(?string $email): PaymentNotification
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getStatus(): ?string
	{
		return $this->status;
	}

	/**
	 * @param string|null $status
	 * @return PaymentNotification
	 */
	public function setStatus(?string $status): PaymentNotification
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getFee(): ?string
	{
		return $this->fee;
	}

	/**
	 * @param string|null $fee
	 * @return PaymentNotification
	 */
	public function setFee(?string $fee): PaymentNotification
	{
		$this->fee = $fee;
		return $this;
	}

}
