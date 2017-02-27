<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent\DispatchSource;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    public function create(): RouteList
    {
        $routes = new RouteList;
        $routes[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
        $routes[] = new Route('<presenter>/<action>', 'Homepage:default');
        $routes[] = new Route('<presenter>/<action>', 'Response:default');

        return $routes;
    }
}
