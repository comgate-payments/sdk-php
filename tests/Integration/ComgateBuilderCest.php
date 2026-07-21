<?php

declare(strict_types=1);

namespace Tests\Integration;

use Codeception\Attribute\Group;
use Comgate\SDK\Client;
use Comgate\SDK\ClientTerminal;
use Comgate\SDK\Comgate;
use Comgate\SDK\Config;
use Comgate\SDK\Http\ITransport;
use Comgate\SDK\Logging\StdoutLogger;
use Tests\Support\IntegrationTester;

class ComgateBuilderCest
{
	#[Group('builder')]
	public function createClientTest(IntegrationTester $I): void
	{
		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->createClient();

		$I->assertInstanceOf(Client::class, $client);
	}

	#[Group('builder')]
	public function createTerminalClientTest(IntegrationTester $I): void
	{
		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->createTerminalClient();

		$I->assertInstanceOf(ClientTerminal::class, $client);
	}

	#[Group('builder')]
	public function customUrlIsAppliedTest(IntegrationTester $I): void
	{
		$customUrl = 'https://custom.example.com/v1/';

		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->setUrl($customUrl)
			->createClient();

		$I->assertInstanceOf(Client::class, $client);
		$I->assertEquals($customUrl, $client->getTransport()->getConfig()->getUrl());
	}

	#[Group('builder')]
	public function defaultUrlIsProductionTest(IntegrationTester $I): void
	{
		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->createClient();

		$config = $client->getTransport()->getConfig();
		$I->assertInstanceOf(ITransport::class, $client->getTransport());
		$I->assertStringStartsWith('https://', $config->getUrl());
	}

	#[Group('builder')]
	public function merchantAndSecretAreStoredInConfigTest(IntegrationTester $I): void
	{
		$merchant = 'test-merchant-123';
		$secret = 'test-secret-abc';

		$client = Comgate::defaults()
			->setMerchant($merchant)
			->setSecret($secret)
			->createClient();

		$config = $client->getTransport()->getConfig();
		$I->assertEquals($merchant, $config->getMerchant());
		$I->assertEquals($secret, $config->getSecret());
	}

	#[Group('builder')]
	public function builderWithLoggerCreatesClientTest(IntegrationTester $I): void
	{
		$logger = new StdoutLogger();

		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->setLogger($logger)
			->createClient();

		$I->assertInstanceOf(Client::class, $client);
	}

	#[Group('builder')]
	public function setUrlWithoutTrailingSlashAddsOneTest(IntegrationTester $I): void
	{
		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->setUrl('https://example.com/v1')
			->createClient();

		$I->assertStringEndsWith('/', $client->getTransport()->getConfig()->getUrl());
	}

	#[Group('builder')]
	public function setUrlWithMultipleTrailingSlashesCollapsesToOneTest(IntegrationTester $I): void
	{
		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->setUrl('https://example.com/v1///')
			->createClient();

		$url = $client->getTransport()->getConfig()->getUrl();

		$I->assertStringEndsWith('/', $url);
		$I->assertStringEndsNotWith('//', $url);
	}

	#[Group('builder')]
	public function setUrlAlreadyWithTrailingSlashIsUnchangedTest(IntegrationTester $I): void
	{
		$url = 'https://example.com/v1/';

		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->setUrl($url)
			->createClient();

		$I->assertEquals($url, $client->getTransport()->getConfig()->getUrl());
	}
}
