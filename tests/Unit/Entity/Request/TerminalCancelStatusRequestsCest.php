<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Request;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Request\TerminalPaymentCancelRequest;
use Comgate\SDK\Entity\Request\TerminalPaymentStatusRequest;
use Comgate\SDK\Entity\Request\TerminalRefundCancelRequest;
use Comgate\SDK\Entity\Request\TerminalRefundStatusRequest;
use Tests\Support\UnitTester;

#[Group('terminal-request')]
class TerminalCancelStatusRequestsCest
{
	// -----------------------------------------------------------------------
	// TerminalPaymentCancelRequest
	// -----------------------------------------------------------------------

	public function terminalPaymentCancelToArrayTest(UnitTester $I): void
	{
		$req = new TerminalPaymentCancelRequest('AB12-CD34');

		$I->assertEquals(['transId' => 'AB12-CD34'], $req->toArray());
	}

	public function terminalPaymentCancelUrnContainsPlaceholderTest(UnitTester $I): void
	{
		$req = new TerminalPaymentCancelRequest('AB12-CD34');

		$I->assertStringContainsString('{transId}', $req->getUrn());
		$I->assertStringContainsString('terminalPayment', $req->getUrn());
	}

	public function terminalPaymentCancelSetTransIdTest(UnitTester $I): void
	{
		$req = new TerminalPaymentCancelRequest('old-id');
		$req->setTransId('new-id');

		$I->assertEquals('new-id', $req->getTransId());
	}

	// -----------------------------------------------------------------------
	// TerminalPaymentStatusRequest
	// -----------------------------------------------------------------------

	public function terminalPaymentStatusToArrayTest(UnitTester $I): void
	{
		$req = new TerminalPaymentStatusRequest('ZZ-99-AB');

		$I->assertEquals(['transId' => 'ZZ-99-AB'], $req->toArray());
	}

	public function terminalPaymentStatusUrnContainsPlaceholderTest(UnitTester $I): void
	{
		$req = new TerminalPaymentStatusRequest('ZZ-99-AB');

		$I->assertStringContainsString('{transId}', $req->getUrn());
		$I->assertStringContainsString('terminalPayment', $req->getUrn());
	}

	// -----------------------------------------------------------------------
	// TerminalRefundCancelRequest
	// -----------------------------------------------------------------------

	public function terminalRefundCancelToArrayTest(UnitTester $I): void
	{
		$req = new TerminalRefundCancelRequest('REF-001');

		$I->assertEquals(['transId' => 'REF-001'], $req->toArray());
	}

	public function terminalRefundCancelUrnContainsPlaceholderTest(UnitTester $I): void
	{
		$req = new TerminalRefundCancelRequest('REF-001');

		$I->assertStringContainsString('{transId}', $req->getUrn());
		$I->assertStringContainsString('terminalRefund', $req->getUrn());
	}

	// -----------------------------------------------------------------------
	// TerminalRefundStatusRequest
	// -----------------------------------------------------------------------

	public function terminalRefundStatusToArrayTest(UnitTester $I): void
	{
		$req = new TerminalRefundStatusRequest('REF-002');

		$I->assertEquals(['transId' => 'REF-002'], $req->toArray());
	}

	public function terminalRefundStatusUrnContainsPlaceholderTest(UnitTester $I): void
	{
		$req = new TerminalRefundStatusRequest('REF-002');

		$I->assertStringContainsString('{transId}', $req->getUrn());
		$I->assertStringContainsString('terminalRefund', $req->getUrn());
	}
}
