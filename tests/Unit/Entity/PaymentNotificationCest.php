<?php

namespace Tests\Unit\Entity;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\PaymentNotification;
use Tests\Support\UnitTester;

class PaymentNotificationCest
{
	public function testPushNotification(UnitTester $I){
		$data = [
		  'merchant' => '112233',
		  'test' => 'false',
		  'price' => '1000',
		  'curr' => 'CZK',
		  'label' => 'Demo eshop platba',
		  'refId' => '1359',
		  'method' => 'CARD_CZ_BS',
		  'email' => 'test@comgate.cz',
		  'transId' => 'XXXX-YYYY-ZZZZ',
		  'secret' => 'foosecretfoosecret',
		  'status' => 'PAID',
		  'fee' => 'unknown',
		  'vs' => '2465',
		];

		$notification = PaymentNotification::createFrom($data);
		$I->assertEquals($data['merchant'], $notification->getMerchant());
		$I->assertEquals($data['test'], $notification->isTest());
		$I->assertEquals($data['curr'], $notification->getCurrency());
		$I->assertEquals($data['label'], $notification->getLabel());
		$I->assertEquals($data['refId'], $notification->getReferenceId());
		$I->assertEquals($data['method'], $notification->getMethod());
		$I->assertEquals($data['email'], $notification->getEmail());
		$I->assertEquals($data['transId'], $notification->getTransactionId());
		$I->assertEquals($data['secret'], $notification->getSecret());
		$I->assertEquals($data['status'], $notification->getStatus());
		$I->assertEquals($data['fee'], $notification->getFee());
		$I->assertEquals($data['vs'], $notification->getVs());

		$I->assertInstanceOf(Money::class, $notification->getPrice());
		$I->assertEquals($data['price'], $notification->getPrice()->get());
	}
}
