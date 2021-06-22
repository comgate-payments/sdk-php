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

}
