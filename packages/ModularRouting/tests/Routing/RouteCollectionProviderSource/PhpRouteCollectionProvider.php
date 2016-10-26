<?php

namespace Symplify\ModularRouting\Tests\Routing\RouteCollectionProviderSource;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symplify\ModularRouting\Contract\Routing\RouteCollectionProviderInterface;

final class PhpRouteCollectionProvider implements RouteCollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteCollection() : RouteCollection
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('my_route', new Route('/hello'));

        return $routeCollection;
    }
}
