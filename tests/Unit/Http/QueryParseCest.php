<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Http\Query;
use Tests\Support\UnitTester;

#[Group('query-parse')]
class QueryParseCest
{
	public function parseEmptyStringReturnsEmptyKeyValuePairTest(UnitTester $I): void
	{
		$result = Query::parse('');
		// empty string → one pair with empty key and empty value
		$I->assertArrayHasKey('', $result);
		$I->assertEquals('', $result['']);
	}

	public function parseSingleKeyValueTest(UnitTester $I): void
	{
		$result = Query::parse('key=value');

		$I->assertCount(1, $result);
		$I->assertEquals('value', $result['key']);
	}

	public function parseMultipleParamsTest(UnitTester $I): void
	{
		$result = Query::parse('a=1&b=2&c=3');

		$I->assertCount(3, $result);
		$I->assertEquals('1', $result['a']);
		$I->assertEquals('2', $result['b']);
		$I->assertEquals('3', $result['c']);
	}

	public function parseDecodesUrlEncodedValueTest(UnitTester $I): void
	{
		// %20 = space, %3D = '='
		$result = Query::parse('msg=hello%20world');
		$I->assertEquals('hello world', $result['msg']);
	}

	public function parseValueWithEqualsSignUsesSplitLimit2Test(UnitTester $I): void
	{
		// "a=b=c" → key=a, value=b=c (limit 2 in explode preserves '=' in value)
		$result = Query::parse('a=b=c');

		$I->assertEquals('b=c', $result['a']);
	}

	public function parsePercent26InValueDecodesCorrectlyTest(UnitTester $I): void
	{
		// The split on '&' happens on the RAW string first, so '%26' in a value is NOT
		// treated as a separator — it is correctly decoded to '&' within the value.
		// This confirms the implementation handles encoded ampersands correctly.
		$result = Query::parse('a=hello%26world&b=foo');

		$I->assertCount(2, $result);
		$I->assertEquals('hello&world', $result['a']);
		$I->assertEquals('foo', $result['b']);
	}

	public function parseMissingValueDefaultsToEmptyStringTest(UnitTester $I): void
	{
		$result = Query::parse('key=');
		$I->assertEquals('', $result['key']);
	}

	public function parseMissingEqualsSignResultsInEmptyValueTest(UnitTester $I): void
	{
		// 'key' without '=' → explode gives ['key'], no index 1 → defaults to ''
		$result = Query::parse('key');
		$I->assertEquals('', $result['key']);
	}

	protected function typicalPaymentNotificationParams(): array
	{
		return [
			'payment notification' => [
				'params' => [
					'merchant' => 'ABCD-1234-5678',
					'test'     => 'true',
					'price'    => '10000',
					'curr'     => 'CZK',
					'label'    => 'Test product',
					'refId'    => 'order-001',
					'payerId'  => '',
					'transId'  => 'ZZ-99-AB',
					'secret'   => 'supersecret',
					'status'   => 'PAID',
					'fee'      => 'unknown',
					'vs'       => '',
				],
			],
		];
	}

	#[DataProvider('typicalPaymentNotificationParams')]
	public function parseTypicalPaymentNotificationParamsTest(UnitTester $I, Example $example): void
	{
		// Use RFC3986 encoding (rawurlencode) so Query::parse (rawurldecode) round-trips correctly
		$input = http_build_query($example['params'], '', '&', PHP_QUERY_RFC3986);
		$result = Query::parse($input);

		foreach ($example['params'] as $key => $value) {
			$I->assertEquals((string) $value, $result[$key]);
		}
	}
}