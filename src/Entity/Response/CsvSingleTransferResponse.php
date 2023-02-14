<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class CsvSingleTransferResponse extends FileResponse
{
	public function __construct(Response $csvSingleTransferResponse)
	{
		$csvJson = $csvSingleTransferResponse->getContent();
		$csvData = json_decode($csvJson, true);

		if (isset($csvData['code']) && isset($csvData['message'])) {
			throw new ApiException($csvData['message'], $csvData['code']);
		}

		$this->setFilename($csvData['nazev'])
			->setFileContent(base64_decode($csvData['csv']));
	}
}
