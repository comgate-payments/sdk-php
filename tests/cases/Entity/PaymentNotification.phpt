<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Gateway;

use Comgate\SDK\Entity\PaymentNotification;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Empty
Toolkit::test(function (): void {
	$notification = PaymentNotification::createFrom([]);

	Assert::equal([
		'merchant' => null,
		'test' => false,
		'price' => null,
		'currency' => null,
		'label' => null,
		'referenceId' => null,
		'email' => null,
		'transactionId' => null,
		'status' => null,
		'free' => null,
	], $notification->toArray());
});

// Globals (empty)
Toolkit::test(function (): void {
	$notification = PaymentNotification::createFromGlobals();

	Assert::equal([
		'merchant' => null,
		'test' => false,
		'price' => null,
		'currency' => null,
		'label' => null,
		'referenceId' => null,
		'email' => null,
		'transactionId' => null,
		'status' => null,
		'free' => null,
	], $notification->toArray());
});

// Globals
Toolkit::test(function (): void {
	$_POST = [
		'merchant' => 'comgate',
		'test' => false,
		'price' => '100',
		'curr' => 'CZK',
		'label' => 'test',
		'refId' => 'foo',
		'email' => 'dev@comgate.cz',
		'transId' => 'bar',
		'status' => 'OK',
		'fee' => '0',
	];

	$notification = PaymentNotification::createFromGlobals();

	Assert::equal([
		'merchant' => 'comgate',
		'test' => false,
		'price' => 100,
		'currency' => 'CZK',
		'label' => 'test',
		'referenceId' => 'foo',
		'email' => 'dev@comgate.cz',
		'transactionId' => 'bar',
		'status' => 'OK',
		'free' => '0',
	], $notification->toArray());
});

// Data
Toolkit::test(function (): void {
	$notification = PaymentNotification::createFrom([
		'merchant' => 'comgate',
		'test' => 'false',
		'price' => '100',
		'curr' => 'CZK',
		'label' => 'test',
		'refId' => 'foo',
		'email' => 'dev@comgate.cz',
		'transId' => 'bar',
		'status' => 'OK',
		'fee' => '0',
	]);

	Assert::equal([
		'merchant' => 'comgate',
		'test' => false,
		'price' => 100,
		'currency' => 'CZK',
		'label' => 'test',
		'referenceId' => 'foo',
		'email' => 'dev@comgate.cz',
		'transactionId' => 'bar',
		'status' => 'OK',
		'free' => '0',
	], $notification->toArray());
});
