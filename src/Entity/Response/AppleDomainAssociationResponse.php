<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\Api\MissingParamException;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Query;
use Comgate\SDK\Http\Response;

class AppleDomainAssociationResponse extends FileResponse
{
	protected string $fileContent;
	/**
	 * @param Response $appleDomainAssociationResponse
	 * @throws ApiException
	 */
	public function __construct(Response $appleDomainAssociationResponse)
	{
		$parsedResponse = Query::parse($appleDomainAssociationResponse->getContent());

		$code = (int) $parsedResponse['code'];
		$message = $parsedResponse['message'];

		switch ($code) {
			case 0:
				$this->setFileContent($parsedResponse['fileContent']);
				break;
			case 1400:
				throw new MissingParamException($message, $code);

			default:
				throw new ApiException($message, $code);
		}
	}

	public function setFileContent(string $fileContent): self
	{
		$this->fileContent = $fileContent;
		return $this;
	}
}
