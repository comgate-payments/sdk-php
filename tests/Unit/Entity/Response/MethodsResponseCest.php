<?php

namespace Tests\Unit\Entity;

use Codeception\Attribute\Group;
use Codeception\Stub;
use Comgate\SDK\Entity\Response\MethodsResponse;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;
use Tests\Support\UnitTester;

class MethodsResponseCest
{
	#[Group('methods')]
	public function getMethodsFailTest(UnitTester $I)
	{
		$responseMock = Stub::make(Response::class, [
			'getContent' => '{"error":{"code":1400,"message":"Unauthorized access!"}}',
		]);

		$I->expectThrowable(ApiException::class, function () use ($responseMock) {
			new MethodsResponse($responseMock);
		});
	}
}
