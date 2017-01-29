<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Tests\Adapter\Nette\Source;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    public function create() : RouteList
    {
        $routes = new RouteList;
        $routes[] = new Route('<presenter>/<action>', 'Homepage:default');

        return $routes;
    }
}

