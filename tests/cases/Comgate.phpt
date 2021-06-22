<?php declare(strict_types = 1);

namespace Tests\Cases;

use Comgate\SDK\Entity\PaymentStatus;
use Comgate\SDK\Logging\FileLogger;
use GuzzleHttp\Psr7\Response;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;
use Tests\Toolkit\FakeComgate;

require_once __DIR__ . '/../bootstrap.php';

// Request / response
Toolkit::test(function (): void {
	$client = FakeComgate::tests()
		->fakeHttp([
			new Response(200, [], 'hello=world'),
		])
		->createClient();

	$res = $client->getStatus(PaymentStatus::create()->withTransactionId('foobar123456'));

	Assert::equal(['hello' => 'world'], $res->getData());
});

// Logger
Toolkit::test(function (): void {
	$log = TEMP_DIR . '/comgate1.log';

	$client = FakeComgate::tests()
		->withLogger(new FileLogger($log))
		->fakeHttp([
			new Response(200, [], 'hello=world'),
		])
		->createClient();

	$client->getStatus(PaymentStatus::create()->withTransactionId('foobar123456'));

	Assert::true(file_exists($log));
	Assert::matchFile(__DIR__ . '/../fixtures/logger.single.log', file_get_contents($log));
});

// Logger (multiple)
Toolkit::test(function (): void {
	$log = TEMP_DIR . '/comgate2.log';

	$client = FakeComgate::tests()
		->withLogger(new FileLogger($log))
		->fakeHttp([
			new Response(200, [], 'hello=world'),
			new Response(200, [], 'hello=world'),
		])
		->createClient();

	$client->getStatus(PaymentStatus::create()->withTransactionId('foobar123456'));
	$client->getStatus(PaymentStatus::create()->withTransactionId('foobar123456'));

	Assert::true(file_exists($log));
	Assert::matchFile(__DIR__ . '/../fixtures/logger.multi.log', file_get_contents($log));
});
