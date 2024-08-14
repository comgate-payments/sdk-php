<?php

namespace Tests\PHPStanRule;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<FuncCall>
 */
class FunctionBackwardsCompatibility implements Rule {

	/** @var array<array<string>> */
	private $functionList;

	public function __construct()
	{
		$this->functionList = get_defined_functions();
	}

	/**
	 * @return class-string<FuncCall>
	 */
	public function getNodeType(): string {
		return FuncCall::class;
	}

	/**
	 * @param FuncCall $node
	 * @param Scope $scope
	 * @return array|\PHPStan\Rules\RuleError[]|string[]
	 */
	public function processNode(Node $node, Scope $scope): array {
		if (!$node->name instanceof Name) {
			return [];
		}

		$name = $node->name->toString();

		if (!in_array($name, $this->functionList['internal'], true)) {
			return [
				sprintf("Function %s() is not available in PHP %s", $name, phpversion())
			];
		}

		return [];
	}
}
