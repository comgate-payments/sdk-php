<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class CsvDownloadResponse extends FileResponse
{
	/**
	 * @param Response $aboSingleTransferResponse
	 * @throws ApiException
	 */
	public function __construct(Response $aboSingleTransferResponse)
	{
		foreach ($aboSingleTransferResponse->getHeader() as $name => $values) {
			foreach ($values as $value) {
				header(sprintf('%s: %s', $name, $value), false);
			}
		}

		echo $aboSingleTransferResponse->getContent();
		exit;
	}
}
