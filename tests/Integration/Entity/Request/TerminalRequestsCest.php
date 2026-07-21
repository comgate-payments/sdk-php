<?php

declare(strict_types=1);

namespace Tests\Integration\Entity\Request;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Request\TerminalPaymentCreateRequest;
use Comgate\SDK\Entity\Request\TerminalRefundCreateRequest;
use Comgate\SDK\Entity\TerminalPayment;
use Comgate\SDK\Entity\TerminalRefund;
use Tests\Support\IntegrationTester;

class TerminalRequestsCest
{
	#[Group('terminal-request')]
	#[DataProvider('getTerminalPaymentScenarios')]
	public function testTerminalPaymentCreateRequestParams(IntegrationTester $I, Example $example): void
	{
		$request = new TerminalPaymentCreateRequest($example['payment']);
		$I->assertEquals($example['result'], $request->toArray());
	}

	protected function getTerminalPaymentScenarios(): array
	{
		return [
			'minimal payment — only required fields' => [
				'payment' => (new TerminalPayment())
					->setPrice(Money::ofInt(100))
					->setCurr(CurrencyCode::CZK),
				'result' => [
					'price' => 10000,
					'curr' => CurrencyCode::CZK,
				],
			],
			'payment with refId' => [
				'payment' => (new TerminalPayment())
					->setPrice(Money::ofInt(50))
					->setCurr(CurrencyCode::EUR)
					->setRefId('order-9999'),
				'result' => [
					'price' => 5000,
					'curr' => CurrencyCode::EUR,
					'refId' => 'order-9999',
				],
			],
			'price in cents' => [
				'payment' => (new TerminalPayment())
					->setPrice(Money::ofCents(150))
					->setCurr(CurrencyCode::CZK),
				'result' => [
					'price' => 150,
					'curr' => CurrencyCode::CZK,
				],
			],
		];
	}

	#[Group('terminal-request')]
	#[DataProvider('getTerminalRefundScenarios')]
	public function testTerminalRefundCreateRequestParams(IntegrationTester $I, Example $example): void
	{
		$request = new TerminalRefundCreateRequest($example['refund']);
		$I->assertEquals($example['result'], $request->toArray());
	}

	protected function getTerminalRefundScenarios(): array
	{
		return [
			'minimal refund — only required fields' => [
				'refund' => (new TerminalRefund())
					->setPrice(Money::ofInt(50))
					->setCurr(CurrencyCode::CZK),
				'result' => [
					'price' => 5000,
					'curr' => CurrencyCode::CZK,
				],
			],
			'refund with refId' => [
				'refund' => (new TerminalRefund())
					->setPrice(Money::ofInt(25))
					->setCurr(CurrencyCode::EUR)
					->setRefId('refund-order-42'),
				'result' => [
					'price' => 2500,
					'curr' => CurrencyCode::EUR,
					'refId' => 'refund-order-42',
				],
			],
		];
	}
}
