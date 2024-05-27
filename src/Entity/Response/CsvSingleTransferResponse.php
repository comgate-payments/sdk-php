<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class CsvSingleTransferResponse extends FileResponse
{
	/**
	 * @param Response $csvSingleTransferResponse
	 * @throws ApiException
	 * @return CsvSingleTransferResponse
	 */
	public function __construct(Response $csvSingleTransferResponse)
	{
		$csvJson = $csvSingleTransferResponse->getContent();
		$csvData = json_decode($csvJson, true);

		if (isset($csvData['code']) && isset($csvData['message'])) {
			throw new ApiException($csvData['message'], $csvData['code']);
		}

		$decodedContent = base64_decode($csvData['csv'], true);
		$this->setFilename($csvData['nazev'])
			->setFileContent($decodedContent !== false ? $decodedContent : '');
	}
}
