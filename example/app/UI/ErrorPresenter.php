<?php declare(strict_types = 1);

namespace App\UI;

use Nette\Application\BadRequestException;
use Nette\Application\Helpers;
use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\Responses\CallbackResponse;
use Nette\Application\Responses\ForwardResponse;
use Tracy\ILogger;

class ErrorPresenter implements IPresenter
{

	private ILogger $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}

	public function run(Request $request): IResponse
	{
		$e = $request->getParameter('exception');

		if ($e instanceof BadRequestException) {
			$this->logger->log('HTTP code ' . $e->getCode() . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ' : ' . $e->getLine(), 'access');
			[$module, , $sep] = Helpers::splitName($request->getPresenterName());
			$errorPresenter = $module . $sep . 'Error4xx';

			return new ForwardResponse($request->setPresenterName($errorPresenter));
		}

		$this->logger->log($e, ILogger::EXCEPTION);

		return new CallbackResponse(function (): void {
			require __DIR__ . '/templates/Error/500.phtml';
		});
	}

}
