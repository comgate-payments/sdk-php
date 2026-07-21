<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\TerminalPayment;
use Comgate\SDK\Entity\TerminalRefund;
use Comgate\SDK\Entity\MotoPayment;
use Tests\Support\UnitTester;

class TerminalMotoEntitiesCest
{
	// -----------------------------------------------------------------------
	// TerminalPayment
	// -----------------------------------------------------------------------

	#[Group('terminal-payment-entity')]
	public function terminalPaymentSetPriceFromIntTest(UnitTester $I): void
	{
		$entity = (new TerminalPayment())->setPrice(10);

		$I->assertEquals(1000, $entity->getPrice()->get());
	}

	#[Group('terminal-payment-entity')]
	public function terminalPaymentSetPriceFromFloatTest(UnitTester $I): void
	{
		$entity = (new TerminalPayment())->setPrice(9.99);

		$I->assertEquals(999, $entity->getPrice()->get());
	}

	#[Group('terminal-payment-entity')]
	public function terminalPaymentSetPriceFromMoneyTest(UnitTester $I): void
	{
		$money = Money::ofCents(1234);
		$entity = (new TerminalPayment())->setPrice($money);

		$I->assertEquals(1234, $entity->getPrice()->get());
	}

	#[Group('terminal-payment-entity')]
	public function terminalPaymentCurrTest(UnitTester $I): void
	{
		$entity = (new TerminalPayment())->setCurr(CurrencyCode::CZK);

		$I->assertEquals(CurrencyCode::CZK, $entity->getCurr());
	}

	#[Group('terminal-payment-entity')]
	public function terminalPaymentRefIdNullByDefaultTest(UnitTester $I): void
	{
		$entity = new TerminalPayment();

		$I->assertNull($entity->getRefId());
	}

	#[Group('terminal-payment-entity')]
	public function terminalPaymentRefIdSetTest(UnitTester $I): void
	{
		$entity = (new TerminalPayment())->setRefId('order-999');

		$I->assertEquals('order-999', $entity->getRefId());
	}

	#[Group('terminal-payment-entity')]
	public function terminalPaymentRefIdClearedWithNullTest(UnitTester $I): void
	{
		$entity = (new TerminalPayment())
			->setRefId('order-999')
			->setRefId(null);

		$I->assertNull($entity->getRefId());
	}

	// -----------------------------------------------------------------------
	// TerminalRefund
	// -----------------------------------------------------------------------

	#[Group('terminal-refund-entity')]
	public function terminalRefundSetPriceFromIntTest(UnitTester $I): void
	{
		$entity = (new TerminalRefund())->setPrice(5);

		$I->assertEquals(500, $entity->getPrice()->get());
	}

	#[Group('terminal-refund-entity')]
	public function terminalRefundCurrTest(UnitTester $I): void
	{
		$entity = (new TerminalRefund())->setCurr(CurrencyCode::EUR);

		$I->assertEquals(CurrencyCode::EUR, $entity->getCurr());
	}

	#[Group('terminal-refund-entity')]
	public function terminalRefundRefIdNullByDefaultTest(UnitTester $I): void
	{
		$entity = new TerminalRefund();

		$I->assertNull($entity->getRefId());
	}

	#[Group('terminal-refund-entity')]
	public function terminalRefundRefIdSetAndClearedTest(UnitTester $I): void
	{
		$entity = (new TerminalRefund())->setRefId('ref-42');

		$I->assertEquals('ref-42', $entity->getRefId());

		$entity->setRefId(null);

		$I->assertNull($entity->getRefId());
	}

	// -----------------------------------------------------------------------
	// MotoPayment
	// -----------------------------------------------------------------------

	#[Group('moto-payment-entity')]
	public function motoPaymentEncryptedCardNumberTest(UnitTester $I): void
	{
		$entity = (new MotoPayment())->setEncryptedCardNumber('enc-card-123');

		$I->assertEquals('enc-card-123', $entity->getEncryptedCardNumber());
	}

	#[Group('moto-payment-entity')]
	public function motoPaymentEncryptedCardExpirationTest(UnitTester $I): void
	{
		$entity = (new MotoPayment())->setEncryptedCardExpiration('enc-exp-456');

		$I->assertEquals('enc-exp-456', $entity->getEncryptedCardExpiration());
	}

	#[Group('moto-payment-entity')]
	public function motoPaymentEncryptedCardCvvTest(UnitTester $I): void
	{
		$entity = (new MotoPayment())->setEncryptedCardCvv('enc-cvv-789');

		$I->assertEquals('enc-cvv-789', $entity->getEncryptedCardCvv());
	}

	#[Group('moto-payment-entity')]
	public function motoPaymentDefaultsAreEmptyStringsTest(UnitTester $I): void
	{
		$entity = new MotoPayment();

		$I->assertEquals('', $entity->getEncryptedCardNumber());
		$I->assertEquals('', $entity->getEncryptedCardExpiration());
		$I->assertEquals('', $entity->getEncryptedCardCvv());
	}

	#[Group('moto-payment-entity')]
	public function motoPaymentInheritsPaymentPriceTest(UnitTester $I): void
	{
		$entity = (new MotoPayment())->setPrice(Money::ofInt(50));

		$I->assertEquals(5000, $entity->getPrice()->get());
	}
}
