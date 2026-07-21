<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Entity\PaymentCard;
use Exception;
use Tests\Support\UnitTester;

#[Group('payment-card')]
class PaymentCardCest
{
	// -----------------------------------------------------------------------
	// Constructor — valid combinations
	// -----------------------------------------------------------------------

	public function constructWithNullsAllowedTest(UnitTester $I): void
	{
		$card = new PaymentCard(null, null, null);

		$I->assertNull($card->getCardNumber());
		$I->assertNull($card->getCardExpiration());
		$I->assertNull($card->getCardCvv());
	}

	public function constructWithValidValuesTest(UnitTester $I): void
	{
		$card = new PaymentCard('1234567890123456', '202512', '123');

		$I->assertEquals('1234567890123456', $card->getCardNumber());
		$I->assertEquals('202512', $card->getCardExpiration());
		$I->assertEquals('123', $card->getCardCvv());
	}

	// -----------------------------------------------------------------------
	// Card number validation
	// -----------------------------------------------------------------------

	#[DataProvider('invalidCardNumbers')]
	public function invalidCardNumberThrowsExceptionTest(UnitTester $I, Example $example): void
	{
		$I->expectThrowable(Exception::class, function () use ($example) {
			new PaymentCard($example['number']);
		});
	}

	protected function invalidCardNumbers(): array
	{
		return [
			'too short'         => ['number' => '123456789012345'],
			'too long'          => ['number' => '12345678901234567'],
			'contains letters'  => ['number' => '123456789012345A'],
			'empty string'      => ['number' => ''],
			'spaces'            => ['number' => '1234 5678 9012 3456'],
		];
	}

	public function validCardNumberNotThrowTest(UnitTester $I): void
	{
		$card = new PaymentCard('4111111111111111');
		$I->assertEquals('4111111111111111', $card->getCardNumber());
	}

	public function setCardNumberValidDoesNotThrowTest(UnitTester $I): void
	{
		$card = new PaymentCard();
		$card->setCardNumber('9999999999999999');
		$I->assertEquals('9999999999999999', $card->getCardNumber());
	}

	public function setCardNumberInvalidThrowsExceptionTest(UnitTester $I): void
	{
		$card = new PaymentCard();
		$I->expectThrowable(Exception::class, function () use ($card) {
			$card->setCardNumber('123');
		});
	}

	// -----------------------------------------------------------------------
	// Card expiration validation
	// -----------------------------------------------------------------------

	#[DataProvider('invalidExpirations')]
	public function invalidExpirationThrowsExceptionTest(UnitTester $I, Example $example): void
	{
		$I->expectThrowable(Exception::class, function () use ($example) {
			new PaymentCard(null, $example['expiry']);
		});
	}

	protected function invalidExpirations(): array
	{
		return [
			'too short'         => ['expiry' => '20251'],
			'too long'          => ['expiry' => '2025121'],
			'contains letters'  => ['expiry' => '2025AB'],
			'empty string'      => ['expiry' => ''],
			'slashes'           => ['expiry' => '2025/12'],
		];
	}

	public function validExpirationNotThrowTest(UnitTester $I): void
	{
		$card = new PaymentCard(null, '202512');
		$I->assertEquals('202512', $card->getCardExpiration());
	}

	public function setExpirationValidDoesNotThrowTest(UnitTester $I): void
	{
		$card = new PaymentCard();
		$card->setCardExpiration('203001');
		$I->assertEquals('203001', $card->getCardExpiration());
	}

	public function setExpirationInvalidThrowsExceptionTest(UnitTester $I): void
	{
		$card = new PaymentCard();
		$I->expectThrowable(Exception::class, function () use ($card) {
			$card->setCardExpiration('12/25');
		});
	}

	// -----------------------------------------------------------------------
	// CVV validation
	// -----------------------------------------------------------------------

	#[DataProvider('invalidCvvs')]
	public function invalidCvvThrowsExceptionTest(UnitTester $I, Example $example): void
	{
		$I->expectThrowable(Exception::class, function () use ($example) {
			new PaymentCard(null, null, $example['cvv']);
		});
	}

	protected function invalidCvvs(): array
	{
		return [
			'too short'        => ['cvv' => '12'],
			'too long'         => ['cvv' => '1234'],
			'contains letters' => ['cvv' => '12A'],
			'empty string'     => ['cvv' => ''],
		];
	}

	public function validCvvNotThrowTest(UnitTester $I): void
	{
		$card = new PaymentCard(null, null, '456');
		$I->assertEquals('456', $card->getCardCvv());
	}

	public function setCvvValidDoesNotThrowTest(UnitTester $I): void
	{
		$card = new PaymentCard();
		$card->setCardCvv('000');
		$I->assertEquals('000', $card->getCardCvv());
	}

	public function setCvvInvalidThrowsExceptionTest(UnitTester $I): void
	{
		$card = new PaymentCard();
		$I->expectThrowable(Exception::class, function () use ($card) {
			$card->setCardCvv('99');
		});
	}
}
