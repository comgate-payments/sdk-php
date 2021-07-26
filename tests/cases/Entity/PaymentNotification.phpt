<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Gateway;

use Comgate\SDK\Entity\PaymentNotification;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Empty
Toolkit::test(function (): void {
	$notification = PaymentNotification::createFrom([]);

	Assert::equal(null, $notification->getMerchant());
	Assert::equal(false, $notification->isTest());
	Assert::equal(null, $notification->getPrice());
	Assert::equal(null, $notification->getCurrency());
	Assert::equal(null, $notification->getLabel());
	Assert::equal(null, $notification->getReferenceId());
	Assert::equal(null, $notification->getEmail());
	Assert::equal(null, $notification->getTransactionId());
	Assert::equal(null, $notification->getStatus());
	Assert::equal(null, $notification->getFee());
});

// Globals (empty)
Toolkit::test(function (): void {
	$notification = PaymentNotification::createFromGlobals();

	Assert::equal(null, $notification->getMerchant());
	Assert::equal(false, $notification->isTest());
	Assert::equal(null, $notification->getPrice());
	Assert::equal(null, $notification->getCurrency());
	Assert::equal(null, $notification->getLabel());
	Assert::equal(null, $notification->getReferenceId());
	Assert::equal(null, $notification->getEmail());
	Assert::equal(null, $notification->getTransactionId());
	Assert::equal(null, $notification->getStatus());
	Assert::equal(null, $notification->getFee());
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

	Assert::equal('comgate', $notification->getMerchant());
	Assert::equal(false, $notification->isTest());
	Assert::equal(100, $notification->getPrice()->get());
	Assert::equal('CZK', $notification->getCurrency());
	Assert::equal('test', $notification->getLabel());
	Assert::equal('foo', $notification->getReferenceId());
	Assert::equal('dev@comgate.cz', $notification->getEmail());
	Assert::equal('bar', $notification->getTransactionId());
	Assert::equal('OK', $notification->getStatus());
	Assert::equal('0', $notification->getFee());
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

	Assert::equal('comgate', $notification->getMerchant());
	Assert::equal(false, $notification->isTest());
	Assert::equal(100, $notification->getPrice()->get());
	Assert::equal('CZK', $notification->getCurrency());
	Assert::equal('test', $notification->getLabel());
	Assert::equal('foo', $notification->getReferenceId());
	Assert::equal('dev@comgate.cz', $notification->getEmail());
	Assert::equal('bar', $notification->getTransactionId());
	Assert::equal('OK', $notification->getStatus());
	Assert::equal('0', $notification->getFee());
});
