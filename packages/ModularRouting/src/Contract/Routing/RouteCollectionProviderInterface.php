<?php declare(strict_types=1);

namespace Symplify\ModularRouting\Contract\Routing;

use Symfony\Component\Routing\RouteCollection;

interface RouteCollectionProviderInterface
{
    public function getRouteCollection() : RouteCollection;
}
