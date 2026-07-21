<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\Logical\ParamIsNotSetException;
use Tests\Support\UnitTester;

/**
 * Documents the behaviour of the newer URL getters on Payment.
 *
 * Payment has two URL getter families:
 *   - Redirect (legacy): getUrlPaidRedirect() / setUrlPaidRedirect()  → key 'url_paid'  (in defaults)
 *   - New:               getUrlPaid()         / setUrlPaid()          → key 'urlPaid'   (NOT in defaults)
 *
 * The new getters throw ParamIsNotSetException when called without a prior setter,
 * because 'urlPaid' / 'urlCancelled' / 'urlPending' are NOT initialised in $params defaults.
 * After calling the matching setter the getter works correctly.
 */
#[Group('payment-url')]
class PaymentUrlGettersCest
{
	// -----------------------------------------------------------------------
	// getUrlPaid — throws without prior set, works after set
	// -----------------------------------------------------------------------

	public function getUrlPaidWithoutSetThrowsTest(UnitTester $I): void
	{
		$payment = new Payment();

		$I->expectThrowable(ParamIsNotSetException::class, function () use ($payment) {
			$payment->getUrlPaid();
		});
	}

	public function setUrlPaidThenGetReturnsValueTest(UnitTester $I): void
	{
		$payment = new Payment();
		$payment->setUrlPaid('https://example.com/paid');

		$I->assertEquals('https://example.com/paid', $payment->getUrlPaid());
	}

	// -----------------------------------------------------------------------
	// getUrlCancelled — throws without prior set, works after set
	// -----------------------------------------------------------------------

	public function getUrlCancelledWithoutSetThrowsTest(UnitTester $I): void
	{
		$payment = new Payment();

		$I->expectThrowable(ParamIsNotSetException::class, function () use ($payment) {
			$payment->getUrlCancelled();
		});
	}

	public function setUrlCancelledThenGetReturnsValueTest(UnitTester $I): void
	{
		$payment = new Payment();
		$payment->setUrlCancelled('https://example.com/cancelled');

		$I->assertEquals('https://example.com/cancelled', $payment->getUrlCancelled());
	}

	// -----------------------------------------------------------------------
	// getUrlPending — throws without prior set, works after set
	// -----------------------------------------------------------------------

	public function getUrlPendingWithoutSetThrowsTest(UnitTester $I): void
	{
		$payment = new Payment();

		$I->expectThrowable(ParamIsNotSetException::class, function () use ($payment) {
			$payment->getUrlPending();
		});
	}

	public function setUrlPendingThenGetReturnsValueTest(UnitTester $I): void
	{
		$payment = new Payment();
		$payment->setUrlPending('https://example.com/pending');

		$I->assertEquals('https://example.com/pending', $payment->getUrlPending());
	}

	// -----------------------------------------------------------------------
	// Legacy redirect getters always return empty string by default
	// -----------------------------------------------------------------------

	public function legacyUrlPaidRedirectDefaultsToEmptyStringTest(UnitTester $I): void
	{
		$payment = new Payment();

		$I->assertEquals('', $payment->getUrlPaidRedirect());
		$I->assertEquals('', $payment->getUrlCancelledRedirect());
		$I->assertEquals('', $payment->getUrlPendingRedirect());
	}

	public function legacyAndNewGettersAreIndependentTest(UnitTester $I): void
	{
		$payment = new Payment();
		$payment->setUrlPaidRedirect('https://legacy.com/paid');
		$payment->setUrlPaid('https://new.com/paid');

		$I->assertEquals('https://legacy.com/paid', $payment->getUrlPaidRedirect());
		$I->assertEquals('https://new.com/paid', $payment->getUrlPaid());
	}

	// -----------------------------------------------------------------------
	// getParam() throws ParamIsNotSetException for missing key
	// -----------------------------------------------------------------------

	public function getParamWithNonExistentKeyThrowsTest(UnitTester $I): void
	{
		$payment = new Payment();

		$I->expectThrowable(ParamIsNotSetException::class, function () use ($payment) {
			$payment->getParam('nonExistentKey');
		});
	}
}
