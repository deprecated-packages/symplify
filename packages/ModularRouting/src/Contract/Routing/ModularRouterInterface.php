<?php

declare(strict_types=1);

namespace Symplify\ModularRouting\Contract\Routing;

use Symfony\Component\Routing\RouterInterface;

interface ModularRouterInterface extends RouterInterface
{
    public function addRouteCollectionProvider(RouteCollectionProviderInterface $routeCollectionProvider);
}
