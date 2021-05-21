<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Gateway;

use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
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

test(function (): void {
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
