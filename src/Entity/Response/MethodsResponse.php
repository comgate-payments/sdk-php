<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Entity\Method;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class MethodsResponse
{
        /**
         *
         * @var array<Method>
         */
	private array $methodsList = [];

	/**
	 * @param Response $methodsResponse
	 * @throws ApiException
	 * @return MethodsResponse
	 */
	public function __construct(Response $methodsResponse)
	{
		$methodsJson = $methodsResponse->getContent();
		$methodsArray = json_decode($methodsJson, true);

		if (isset($methodsArray['methods'])) {
			foreach ((array)$methodsArray['methods'] as $methodData) {
				$method = (new Method())->fromArray((array)$methodData);

				$this->methodsList[] = $method;
			}
		}

		if (isset($methodsArray['error'])) {
			$code = (int)$methodsArray['error']['code'];
			$message = $methodsArray['error']['message'];

			throw new ApiException((string)$message, $code);
		}
	}

	/**
	 * @return array<Method>
	 */
	public function getMethodsList(): array
	{
		return $this->methodsList;
	}

	/**
	 * @param array<Method> $methodsList
	 * @return MethodsResponse
	 */
	public function setMethodsList(array $methodsList): MethodsResponse
	{
		$this->methodsList = $methodsList;
		return $this;
	}

        /**
         *
         * @return array<int<0, max>, array<string, mixed>>.
         */
	public function toArray(): array
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
