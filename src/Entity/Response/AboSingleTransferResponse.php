<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Http\Response;

class AboSingleTransferResponse extends FileResponse
{
	/**
	 * @param Response $aboSingleTransferResponse
	 * @throws ApiException
	 */
	public function __construct(Response $aboSingleTransferResponse)
	{
		$aboJson = $aboSingleTransferResponse->getContent();
		$aboData = json_decode($aboJson, true);

		if (isset($aboData['code']) && isset($aboData['message'])) {
			throw new ApiException($aboData['message'], $aboData['code']);
		}

		$this->setFilename($aboData['nazev'])
			->setFileContent(base64_decode($aboData['abo'], true));
	}
}
