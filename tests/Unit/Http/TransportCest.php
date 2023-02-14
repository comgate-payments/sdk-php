<?php

namespace Tests\Unit\Http;

use Codeception\Attribute\Group;
use Comgate\SDK\Comgate;
use Comgate\SDK\Config;
use Comgate\SDK\Http\Transport;
use Tests\Support\UnitTester;

class TransportCest
{
	#[Group('transport')]
	public function testPostException(UnitTester $I)
	{
		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->setUrl($_ENV['API_URL'])
			->createClient();
		$config = new Config('merchant', 'secret');

		$transport = new Transport($this->getClient(), $config);
	}
}
