<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Entity\Method;
use Comgate\SDK\Http\Response;

class MethodsResponse
{

	private array $methodsList = [];

	public function __construct(Response $methodsResponse)
	{
		$methodsJson = $methodsResponse->getContent();
		$methodsArray = json_decode($methodsJson, true);

		foreach ($methodsArray['methods'] as $methodData) {
			$method = (new Method())->fromArray($methodData);

			$this->methodsList[] = $method;
		}
	}

	/**
	 * @return array
	 */
	public function getMethodsList(): array
	{
		return $this->methodsList;
	}

	/**
	 * @param array $methodsList
	 * @return MethodsResponse
	 */
	public function setMethodsList(array $methodsList): MethodsResponse
	{
		$this->methodsList = $methodsList;
		return $this;
	}

	public function toArray()
	{
		$methodsArray = [];

		foreach ($this->methodsList as $method) {
			$methodsArray[] = [
				'id' => $method->getId(),
				'name' => $method->getName(),
				'description' => $method->getDescription(),
				'logo' => $method->getLogo(),
			];
		}

		return $methodsArray;
	}

}
