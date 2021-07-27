<?php declare(strict_types = 1);

namespace Tests\Cases;

use Nette\Application\Application;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

// Request / response
Toolkit::test(function (): void {
	$container = require __DIR__ . '/../../app/bootstrap.php';
	Assert::type(Application::class, $container->getByType(Application::class));
});
