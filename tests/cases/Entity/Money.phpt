<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Gateway;

use Comgate\SDK\Entity\Money;
use Comgate\SDK\Exception\LogicalException;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Basic
Toolkit::test(function (): void {
	$data = [
		[5, 500, 5.0],
		[105, 10500, 105.0],
		[105.99, 10599, 105.99],
	];

	foreach ($data as [$value, $output, $outputReal]) {
		$money = Money::of($value);

		Assert::equal($output, $money->get());
		Assert::equal($outputReal, $money->getReal());
	}
});

// Exception (invalid float)
Toolkit::test(function (): void {
	Assert::exception(function (): void {
		Money::of(105.123);
	}, LogicalException::class, 'The price must be a maximum of two valid decimal numbers.');
});
