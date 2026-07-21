<?php

declare(strict_types=1);

namespace Tests\Integration\Entity;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\PaymentNotification;
use Tests\Support\IntegrationTester;

class PaymentNotificationCest
{
	#[Group('notification')]
	public function createFromCompletePayloadTest(IntegrationTester $I): void
	{
		$data = [
			'merchant' => '123456',
			'test' => 'true',
			'price' => '10000',
			'curr' => 'CZK',
			'label' => 'Test product',
			'refId' => 'order-42',
			'email' => 'customer@example.com',
			'transId' => 'AB12-CD34-EF56',
			'status' => 'PAID',
			'fee' => '0.50',
			'vs' => '9876543210',
			'method' => 'CARD_ALL',
			'secret' => 'my-secret',
		];

		$notification = PaymentNotification::createFrom($data);

		$I->assertInstanceOf(PaymentNotification::class, $notification);
		$I->assertEquals('123456', $notification->getMerchant());
		$I->assertTrue($notification->isTest());
		$I->assertInstanceOf(Money::class, $notification->getPrice());
		$I->assertEquals(10000, $notification->getPrice()->get());
		$I->assertEquals('CZK', $notification->getCurrency());
		$I->assertEquals('Test product', $notification->getLabel());
		$I->assertEquals('order-42', $notification->getReferenceId());
		$I->assertEquals('customer@example.com', $notification->getEmail());
		$I->assertEquals('AB12-CD34-EF56', $notification->getTransactionId());
		$I->assertEquals('PAID', $notification->getStatus());
		$I->assertEquals('0.50', $notification->getFee());
		$I->assertEquals('9876543210', $notification->getVs());
		$I->assertEquals('CARD_ALL', $notification->getMethod());
		$I->assertEquals('my-secret', $notification->getSecret());
	}

	#[Group('notification')]
	public function createFromMissingFieldsTest(IntegrationTester $I): void
	{
		$notification = PaymentNotification::createFrom([]);

		$I->assertInstanceOf(PaymentNotification::class, $notification);
		$I->assertNull($notification->getMerchant());
		$I->assertNull($notification->getTransactionId());
		$I->assertNull($notification->getStatus());
		$I->assertNull($notification->getPrice());
		$I->assertNull($notification->getCurrency());
		$I->assertNull($notification->getEmail());
		$I->assertNull($notification->getLabel());
		$I->assertNull($notification->getReferenceId());
		$I->assertNull($notification->getFee());
		$I->assertNull($notification->getVs());
		$I->assertNull($notification->getMethod());
		$I->assertNull($notification->getSecret());
	}

	#[Group('notification')]
	public function isTestBooleanConversionTest(IntegrationTester $I): void
	{
		$notificationTrue = PaymentNotification::createFrom(['test' => 'true']);
		$I->assertTrue($notificationTrue->isTest());

		$notificationFalse = PaymentNotification::createFrom(['test' => 'false']);
		$I->assertFalse($notificationFalse->isTest());

		$notificationOne = PaymentNotification::createFrom(['test' => '1']);
		$I->assertTrue($notificationOne->isTest());

		$notificationZero = PaymentNotification::createFrom(['test' => '0']);
		$I->assertFalse($notificationZero->isTest());
	}

	#[Group('notification')]
	public function createFactoryReturnsEmptyObjectTest(IntegrationTester $I): void
	{
		$notification = PaymentNotification::create();

		$I->assertInstanceOf(PaymentNotification::class, $notification);
		$I->assertNull($notification->getTransactionId());
		$I->assertNull($notification->getMerchant());
	}

	#[Group('notification')]
	public function settersWorkCorrectlyTest(IntegrationTester $I): void
	{
		$notification = PaymentNotification::createFrom(['vs' => '111', 'method' => 'BANK_ALL']);

		$notification->setVs('999');
		$notification->setMethod('CARD_ALL');
		$notification->setSecret('new-secret');

		$I->assertEquals('999', $notification->getVs());
		$I->assertEquals('CARD_ALL', $notification->getMethod());
		$I->assertEquals('new-secret', $notification->getSecret());
	}
}
