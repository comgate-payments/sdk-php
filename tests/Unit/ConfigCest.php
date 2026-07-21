<?php

declare(strict_types=1);

namespace Tests\Unit;

use Codeception\Attribute\Group;
use Comgate\SDK\Config;
use Tests\Support\UnitTester;

#[Group('config')]
class ConfigCest
{
	public function defaultUrlIsProductionWithTrailingSlashTest(UnitTester $I): void
	{
		$config = new Config('merchant', 'secret');

		$I->assertStringStartsWith('https://', $config->getUrl());
		$I->assertStringEndsWith('/', $config->getUrl());
	}

	public function getMerchantAndSecretTest(UnitTester $I): void
	{
		$config = new Config('my-merchant', 'my-secret');

		$I->assertEquals('my-merchant', $config->getMerchant());
		$I->assertEquals('my-secret', $config->getSecret());
	}

	public function setMerchantAndSecretTest(UnitTester $I): void
	{
		$config = new Config('old', 'old');
		$config->setMerchant('new-merchant');
		$config->setSecret('new-secret');

		$I->assertEquals('new-merchant', $config->getMerchant());
		$I->assertEquals('new-secret', $config->getSecret());
	}

	public function customUrlWithoutTrailingSlashGetsSlashAddedTest(UnitTester $I): void
	{
		$config = new Config('m', 's', 'https://example.com/v1');

		$I->assertEquals('https://example.com/v1/', $config->getUrl());
	}

	public function customUrlWithTrailingSlashIsUnchangedTest(UnitTester $I): void
	{
		$config = new Config('m', 's', 'https://example.com/v1/');

		$I->assertEquals('https://example.com/v1/', $config->getUrl());
	}

	public function customUrlWithMultipleTrailingSlashesCollapsedToOneTest(UnitTester $I): void
	{
		$config = new Config('m', 's', 'https://example.com/v1///');

		$url = $config->getUrl();

		$I->assertStringEndsWith('/', $url);
		$I->assertStringEndsNotWith('//', $url);
	}

	public function setUrlAddsTrailingSlashTest(UnitTester $I): void
	{
		$config = new Config('m', 's');
		$config->setUrl('https://staging.example.com');

		$I->assertEquals('https://staging.example.com/', $config->getUrl());
	}
}
