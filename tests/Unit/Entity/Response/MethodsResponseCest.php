<?php

namespace Tests\Unit\Entity;

use Codeception\Attribute\Group;
use Codeception\Stub;
use Comgate\SDK\Entity\Method;
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

	public function getMethodsTest(UnitTester $I)
	{
		$responseMock = Stub::make(Response::class, [
			'getContent' => '{"methods":[{"id":"CARD_CZ_COMGATE","group":"CARD","groupLabel":"A","name":"B","description":"C","logo":"D"},{"id":"BANK_CZ_OTHER","group":"BANK","groupLabel":"E","name":"F","description":"G","logo":"H"}]}',
		]);

		$list = (new MethodsResponse($responseMock))->getMethodsList();

		$I->assertEquals('CARD_CZ_COMGATE', $list[0]->getId());
		$I->assertEquals('CARD', $list[0]->getGroup());
		$I->assertEquals('A', $list[0]->getGroupLabel());
		$I->assertEquals('B', $list[0]->getName());
		$I->assertEquals('C', $list[0]->getDescription());
		$I->assertEquals('D', $list[0]->getLogo());

		$I->assertEquals('BANK_CZ_OTHER', $list[1]->getId());
		$I->assertEquals('BANK', $list[1]->getGroup());
		$I->assertEquals('E', $list[1]->getGroupLabel());
		$I->assertEquals('F', $list[1]->getName());
		$I->assertEquals('G', $list[1]->getDescription());
		$I->assertEquals('H', $list[1]->getLogo());

		$array = $list[0]->toArray();
		$I->assertEquals($array, (new Method())->fromArray($array)->toArray());
	}
}
