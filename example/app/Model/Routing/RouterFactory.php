<?php declare(strict_types = 1);

namespace App\Model\Routing;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

class RouterFactory
{

	public static function createRouter(): Router
	{
		$router = new RouteList();
		$router[] = new Route('<presenter>/<action>', 'Home:default');

		return $router;
	}

}
