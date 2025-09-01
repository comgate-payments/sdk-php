<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class CsvDownloadResponse extends FileResponse
{
	/**
	 * @param Response $csvDownloadResponse
	 * @throws ApiException
	 */
	public function __construct(Response $csvDownloadResponse)
	{
		foreach ($csvDownloadResponse->getHeader() as $name => $values) {
			foreach ($values as $value) {
				header(sprintf('%s: %s', $name, $value), false);
			}
		}

		echo $csvDownloadResponse->getContent();
		exit;
	}
}
