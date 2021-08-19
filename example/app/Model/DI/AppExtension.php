<?php declare(strict_types = 1);

namespace App\Model\DI;

use App\Model\Debug\MultiLogger;
use App\Model\Debug\StdoutLogger;
use App\Model\Debug\TracyLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\PhpGenerator\ClassType;

class AppExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('tracy.logger'))
			->setFactory(MultiLogger::class)
			->setArguments([
				[
					new Statement(TracyLogger::class),
					new Statement(StdoutLogger::class),
				],
			]);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('tracy.logger')
			->setAutowired(false);
	}

	public function afterCompile(ClassType $class): void
	{
		$builder = $this->getContainerBuilder();
		$initialize = $this->initialization ?? $class->getMethod('initialize');
		$initialize->addBody($builder->formatPhp('Tracy\Debugger::setLogger($this->getService(?));', [$this->prefix('tracy.logger')]));
	}

}
