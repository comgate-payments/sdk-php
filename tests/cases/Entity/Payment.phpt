<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Gateway;

use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\LogicalException;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Basic
Toolkit::test(function (): void {
	$payment = Payment::create()
		->withRedirect()
		->withPrice(Money::of(105))
		->withCurrency(CurrencyCode::CZK)
		->withLabel('Test item')
		->withReferenceId('test001')
		->withEmail('dev@comgate.cz')
		->withMethod(PaymentMethodCode::ALL);

	Assert::equal([
		'price' => 10500,
		'curr' => CurrencyCode::CZK,
		'label' => 'Test item',
		'refId' => 'test001',
		'method' => 'ALL',
		'email' => 'dev@comgate.cz',
		'prepareOnly' => 'true',
	], $payment->toArray());
});

// Multiple methods
Toolkit::test(function (): void {
	$payment = Payment::create()
		->withRedirect()
		->withPrice(Money::of(105))
		->withCurrency(CurrencyCode::CZK)
		->withLabel('Test item')
		->withReferenceId('test001')
		->withEmail('dev@comgate.cz')
		->withMethod(PaymentMethodCode::BANK_ERA_BUTTON)
		->withMethod(PaymentMethodCode::BANK_FIO_BUTTON)
		->withoutMethod(PaymentMethodCode::BANK_CSOB_TRANSFER);

	Assert::equal([
		'price' => 10500,
		'curr' => CurrencyCode::CZK,
		'label' => 'Test item',
		'refId' => 'test001',
		'method' => 'BANK_CZ_PS_P+BANK_CZ_FB-BANK_CZ_CSOB',
		'email' => 'dev@comgate.cz',
		'prepareOnly' => 'true',
	], $payment->toArray());
});

// Exception (one allowed method)
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		Payment::create()
			->withRedirect()
			->withPrice(Money::of(105))
			->withCurrency(CurrencyCode::CZK)
			->withLabel('Test item')
			->withReferenceId('test001')
			->withEmail('dev@comgate.cz')
			->withoutMethod(PaymentMethodCode::BANK_CSOB_TRANSFER)
			->toArray();
	}, LogicalException::class, 'There must be at least one allowed method');
});

// Basic (iframe)
Toolkit::test(function (): void {
	$payment = Payment::create()
		->withIframe()
		->withPrice(Money::of(105))
		->withCurrency(CurrencyCode::CZK)
		->withLabel('Test item')
		->withReferenceId('test001')
		->withEmail('dev@comgate.cz')
		->withMethod(PaymentMethodCode::ALL);

	Assert::equal([
		'price' => 10500,
		'curr' => CurrencyCode::CZK,
		'label' => 'Test item',
		'refId' => 'test001',
		'method' => 'ALL',
		'email' => 'dev@comgate.cz',
		'prepareOnly' => 'true',
		'embedded' => 'true',
	], $payment->toArray());
});
