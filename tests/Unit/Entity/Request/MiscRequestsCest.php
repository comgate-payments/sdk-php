<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Request;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Request\SimulationRequest;
use Comgate\SDK\Entity\Request\CsvSingleTransferRequest;
use Comgate\SDK\Entity\Request\CsvDownloadRequest;
use Comgate\SDK\Entity\Request\AboDownloadRequest;
use Tests\Support\UnitTester;

class MiscRequestsCest
{
	// -----------------------------------------------------------------------
	// SimulationRequest
	// -----------------------------------------------------------------------

	#[Group('simulation-request')]
	public function simulationRequestUrnTest(UnitTester $I): void
	{
		$req = new SimulationRequest([]);
		$I->assertEquals('simulation.json', $req->getUrn());
	}

	#[Group('simulation-request')]
	public function simulationRequestToArrayReturnsParamsTest(UnitTester $I): void
	{
		$params = ['transId' => 'AB12-CD34', 'status' => 'PAID'];
		$req = new SimulationRequest($params);

		$I->assertEquals($params, $req->toArray());
	}

	#[Group('simulation-request')]
	public function simulationRequestEmptyParamsTest(UnitTester $I): void
	{
		$req = new SimulationRequest([]);
		$I->assertEquals([], $req->toArray());
	}

	#[Group('simulation-request')]
	public function simulationRequestSetParamsTest(UnitTester $I): void
	{
		$req = new SimulationRequest([]);
		$req->setParams(['transId' => 'ZZ99']);

		$I->assertEquals(['transId' => 'ZZ99'], $req->toArray());
	}

	// -----------------------------------------------------------------------
	// CsvSingleTransferRequest
	// -----------------------------------------------------------------------

	#[Group('csv-single-transfer-request')]
	public function csvSingleTransferToArrayTest(UnitTester $I): void
	{
		$req = new CsvSingleTransferRequest('T-123', false);

		$result = $req->toArray();

		$I->assertEquals('T-123', $result['transferId']);
		$I->assertEquals('false', $result['test']);
	}

	#[Group('csv-single-transfer-request')]
	public function csvSingleTransferUrnContainsTransferIdAndTestParamTest(UnitTester $I): void
	{
		$req = new CsvSingleTransferRequest('T-abc', true);
		$urn = $req->getUrn();

		$I->assertStringContainsString('T-abc', $urn);
		$I->assertStringContainsString('test=true', $urn);
	}

	#[Group('csv-single-transfer-request')]
	public function csvSingleTransferUrlencodesTransferIdTest(UnitTester $I): void
	{
		$req = new CsvSingleTransferRequest('T/special?id', false);
		$urn = $req->getUrn();

		$I->assertStringNotContainsString('T/special?id', $urn);
	}

	// -----------------------------------------------------------------------
	// CsvDownloadRequest
	// -----------------------------------------------------------------------

	#[Group('csv-download-request')]
	public function csvDownloadToArrayTest(UnitTester $I): void
	{
		$req = new CsvDownloadRequest('2024-01', false);

		$result = $req->toArray();

		$I->assertEquals('2024-01', $result['date']);
		$I->assertEquals('false', $result['test']);
	}

	#[Group('csv-download-request')]
	public function csvDownloadUrnContainsDateAndTestParamTest(UnitTester $I): void
	{
		$req = new CsvDownloadRequest('2024-06', true);
		$urn = $req->getUrn();

		$I->assertStringContainsString('2024-06', $urn);
		$I->assertStringContainsString('test=true', $urn);
	}

	// -----------------------------------------------------------------------
	// AboDownloadRequest
	// -----------------------------------------------------------------------

	#[Group('abo-download-request')]
	public function aboDownloadToArrayTest(UnitTester $I): void
	{
		$req = new AboDownloadRequest('2024-01', 'VYPIS', false, 'UTF-8');

		$result = $req->toArray();

		$I->assertEquals('2024-01', $result['date']);
		$I->assertEquals('VYPIS', $result['type']);
		$I->assertEquals('false', $result['test']);
		$I->assertEquals('UTF-8', $result['encoding']);
	}

	#[Group('abo-download-request')]
	public function aboDownloadUrnContainsParamsTest(UnitTester $I): void
	{
		$req = new AboDownloadRequest('2024-03', 'POHYBY', true, 'CP1250');
		$urn = $req->getUrn();

		$I->assertStringContainsString('2024-03', $urn);
		$I->assertStringContainsString('test=true', $urn);
		$I->assertStringContainsString('encoding=CP1250', $urn);
		$I->assertStringContainsString('type=POHYBY', $urn);
	}

	#[Group('abo-download-request')]
	public function aboDownloadUrnOmitsTypeWhenEmptyTest(UnitTester $I): void
	{
		$req = new AboDownloadRequest('2024-03', '', false, 'UTF-8');
		$urn = $req->getUrn();

		$I->assertStringNotContainsString('type=', $urn);
	}
}
