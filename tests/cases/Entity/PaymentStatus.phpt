<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Gateway;

use Comgate\SDK\Entity\PaymentStatus;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Basic
Toolkit::test(function (): void {
	$status = PaymentStatus::create()
		->withTransactionId('123456ABCDEF');

	Assert::equal([
		'transId' => '123456ABCDEF',
	], $status->toArray());
});
