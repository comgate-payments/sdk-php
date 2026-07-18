<?php

namespace Tests\Unit\Entity;

use Codeception\Stub;
use Comgate\SDK\Entity\Response\PaymentStatusResponse;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

class PaymentStatusResponseCest
{
	public function statusWithoutMessageTest(UnitTester $I)
	{
		$responseMock = Stub::make(Response::class, [
			'getContent' => '{"code":0,"transId":"AB12-CD34-EF56","status":"PAID","price":"10000","curr":"CZK"}',
		]);

		$response = new PaymentStatusResponse($responseMock);

		$I->assertEquals(0, $response->getCode());
		$I->assertEquals('', $response->getMessage());
		$I->assertEquals('AB12-CD34-EF56', $response->getTransId());
		$I->assertEquals('PAID', $response->getStatus());
	}
}
