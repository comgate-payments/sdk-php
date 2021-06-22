<?php declare(strict_types = 1);

use Contributte\Bootstrap\ExtraConfigurator;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new ExtraConfigurator();

//$configurator->setDebugMode(true);
//$configurator->setDebugMode(['10.0.0.1']);
$configurator->enableTracy(__DIR__ . '/../var/log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../var/tmp');

$configurator->addConfig(__DIR__ . '/../config/config.neon');

if (file_exists(__DIR__ . '/../config/local.neon')) {
	$configurator->addConfig(__DIR__ . '/../config/local.neon');
}

return $configurator->createContainer();
