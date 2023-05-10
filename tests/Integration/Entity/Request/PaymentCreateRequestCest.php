<?php

namespace Tests\Integration\Entity\Request;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\Request\PaymentCreateRequest;
use Tests\Support\IntegrationTester;
use Codeception\Attribute\Group;

class PaymentCreateRequestCest
{
	#[Group('request')]
	#[DataProvider('getPaymentScenarios')]
	public function testRequestParams(IntegrationTester $I, Example $exampleParams){
		$paymentCreateRequest = new PaymentCreateRequest($exampleParams['payment']);
		$I->assertEquals($exampleParams['result'], $paymentCreateRequest->toArray());
	}

	protected function getPaymentScenarios(){
		return [
			'default params' => [
				'payment' => (new Payment())
					->setPrice(Money::ofInt(123))
				,
				'result' => [
					'test' => 'false',
					'prepareOnly' => 'true',
					'preauth' => 'false',
					'verification' => 'false',
					'embedded' => 'false',
					'method' => '',
					'initRecurring' => 'false',
					'account' => '',
					'name' => '',
					'price' => 12300,
					'dynamicExpiration' => 'false',
				],
			],
			'custom params' => [
				'payment' => (new Payment())
					->setPrice(Money::ofCents(456))
					->setMethods([PaymentMethodCode::ALL_BANKS])
					->addMethod(PaymentMethodCode::BANK_RB_BUTTON)
					->setoutMethod(PaymentMethodCode::BANK_FIO_BUTTON)
					->setInitRecurring(true)
					->setTest(true)
					->setPreauth(true)
					->setEmbedded(true)
					->setAccount('123456')
					->setName('product name')
				,
				'result' => [
					'initRecurring' => 'true',
					'test' => 'true',
					'prepareOnly' => 'true',
					'preauth' => 'true',
					'verification' => 'false',
					'embedded' => 'true',
					'method' => 'BANK_ALL+BANK_CZ_RB-BANK_CZ_FB_P',
					'account' => '123456',
					'name' => 'product name',
					'price' => 456,
					'dynamicExpiration' => 'false',
				],
			],
		];
	}
}
