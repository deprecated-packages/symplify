<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Renderable\Routing;

use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;

interface RouteCollectorInterface
{
    public function addRoute(RouteInterface $route): void;
}
