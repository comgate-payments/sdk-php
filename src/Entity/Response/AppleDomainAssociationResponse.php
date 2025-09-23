<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Query;
use Comgate\SDK\Http\Response;

class AppleDomainAssociationResponse
{
	protected string $fileContent;
	/**
	 * @param Response $appleDomainAssociationResponse
	 * @throws ApiException
	 */
	public function __construct(Response $appleDomainAssociationResponse)
	{
		$parsedResponse = Query::parse($appleDomainAssociationResponse->getContent());

		if (isset($parsedResponse['code']) && isset($parsedResponse['message'])) {
			$code = (int) $parsedResponse['code'];
			$message = $parsedResponse['message'];

			switch ($code) {
				case 1400:
					throw new MissingParamException($message, $code);

				default:
					throw new ApiException($message, $code);
			}
		} else if (isset($parsedResponse['fileContent'])) {
			$this->setFileContent($parsedResponse['fileContent']);
		}
	}

	public function setFileContent(string $fileContent): self
	{
		$this->fileContent = $fileContent;
		return $this;
	}
}
