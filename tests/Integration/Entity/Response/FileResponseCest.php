<?php

namespace Tests\Integration\Entity\Response;

use Codeception\Attribute\Group;
use Comgate\SDK\Entity\Response\FileResponse;
use Comgate\SDK\Exception\LogicalException;
use Tests\Support\IntegrationTester;

class FileResponseCest
{
	#[Group('file')]
	public function testFileSavingError(IntegrationTester $I)
	{
		$fileResponse = new FileResponse();
		$I->expectThrowable(LogicalException::class, function () use ($fileResponse) {
			$fileResponse->saveToFile('directory-does-not-exists');
		});
	}
}
