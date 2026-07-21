<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Response;

use Codeception\Attribute\Group;
use Codeception\Stub;
use Comgate\SDK\Entity\PaymentInfo;
use Comgate\SDK\Entity\Response\SingleTransferResponse;
use Comgate\SDK\Entity\Response\TransferListResponse;
use Comgate\SDK\Entity\Transfer;
use Comgate\SDK\Http\Response;
use Exception;
use Tests\Support\UnitTester;

#[Group('transfer-response')]
class TransferResponsesCest
{
	// -----------------------------------------------------------------------
	// TransferListResponse
	// -----------------------------------------------------------------------

	public function transferListResponseHydratesTransferObjectsTest(UnitTester $I): void
	{
		$data = [
			[
				'transferId'           => 1,
				'transferDate'         => '2023-02-01',
				'accountCounterparty'  => '0/0000',
				'accountOutgoing'      => '1/0000',
				'variableSymbol'       => '0123456789',
			],
			[
				'transferId'           => 2,
				'transferDate'         => '2023-02-02',
				'accountCounterparty'  => '2/0200',
				'accountOutgoing'      => '3/0300',
				'variableSymbol'       => '9876543210',
			],
		];

		$mock = Stub::make(Response::class, ['getContent' => json_encode($data)]);

		$response = new TransferListResponse($mock);
		$list = $response->getTransferList();

		$I->assertCount(2, $list);
		$I->assertInstanceOf(Transfer::class, $list[0]);
		$I->assertEquals(1, $list[0]->getTransferId());
		$I->assertEquals('0/0000', $list[0]->getAccountCounterparty());
		$I->assertEquals('1/0000', $list[0]->getAccountOutgoing());
		$I->assertEquals('0123456789', $list[0]->getVariableSymbol());
	}

	public function transferListResponseEmptyArrayReturnsEmptyListTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, ['getContent' => json_encode([])]);

		$response = new TransferListResponse($mock);

		$I->assertIsArray($response->getTransferList());
		$I->assertEmpty($response->getTransferList());
	}

	// -----------------------------------------------------------------------
	// Transfer::fromArray() — entity hydration
	// -----------------------------------------------------------------------

	public function transferFromArrayHydratesAllFieldsTest(UnitTester $I): void
	{
		$transfer = (new Transfer())->fromArray([
			'transferId'           => 999,
			'transferDate'         => '2024-06-15',
			'accountCounterparty'  => '100/0100',
			'accountOutgoing'      => '200/0200',
			'variableSymbol'       => '5555555555',
		]);

		$I->assertEquals(999, $transfer->getTransferId());
		$I->assertEquals('100/0100', $transfer->getAccountCounterparty());
		$I->assertEquals('200/0200', $transfer->getAccountOutgoing());
		$I->assertEquals('5555555555', $transfer->getVariableSymbol());
		$I->assertInstanceOf(\DateTimeInterface::class, $transfer->getTransferDate());
		$I->assertEquals('2024-06-15', $transfer->getTransferDate()->format('Y-m-d'));
	}

	public function transferFromArrayInvalidDateThrowsTest(UnitTester $I): void
	{
		$I->expectThrowable(Exception::class, function () {
			(new Transfer())->fromArray([
				'transferId'           => 1,
				'transferDate'         => 'not-a-date',
				'accountCounterparty'  => '0/0000',
				'accountOutgoing'      => '1/0000',
				'variableSymbol'       => '1234567890',
			]);
		});
	}

	// -----------------------------------------------------------------------
	// SingleTransferResponse
	// -----------------------------------------------------------------------

	public function singleTransferResponseHydratesPaymentInfoObjectsTest(UnitTester $I): void
	{
		$data = [
			['transId' => 'AB12', 'status' => 'PAID', 'amount' => 10000],
			['transId' => 'CD34', 'status' => 'PENDING', 'amount' => 5000],
		];

		$mock = Stub::make(Response::class, ['getContent' => json_encode($data)]);

		$response = new SingleTransferResponse($mock);
		$list = $response->getPaymentsList();

		$I->assertCount(2, $list);
		$I->assertInstanceOf(PaymentInfo::class, $list[0]);
		$I->assertEquals(['transId' => 'AB12', 'status' => 'PAID', 'amount' => 10000], $list[0]->getData());
	}

	public function singleTransferResponseEmptyJsonReturnsEmptyListTest(UnitTester $I): void
	{
		$mock = Stub::make(Response::class, ['getContent' => json_encode([])]);

		$response = new SingleTransferResponse($mock);

		$I->assertIsArray($response->getPaymentsList());
		$I->assertEmpty($response->getPaymentsList());
	}

	// -----------------------------------------------------------------------
	// PaymentInfo — fromArray() and getData()
	// -----------------------------------------------------------------------

	public function paymentInfoFromArrayStoresDataTest(UnitTester $I): void
	{
		$data = ['transId' => 'XY99', 'amount' => 2500, 'currency' => 'CZK'];
		$info = (new PaymentInfo())->fromArray($data);

		$I->assertEquals($data, $info->getData());
	}

	public function paymentInfoSetDataReplacesExistingTest(UnitTester $I): void
	{
		$info = new PaymentInfo();
		$info->fromArray(['key' => 'old']);
		$info->setData(['key' => 'new']);

		$I->assertEquals(['key' => 'new'], $info->getData());
	}
}
