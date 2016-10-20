<?php

namespace Symplify\ModularRouting\Tests\Routing\AbstractRouteCollectionProviderSource;

use Symfony\Component\Routing\RouteCollection;
use Symplify\ModularRouting\Routing\AbstractRouteCollectionProvider;

final class FilesRouteCollectionProvider extends AbstractRouteCollectionProvider
{
    /**
     * {@inheritdoc}
     */
    public function getRouteCollection() : RouteCollection
    {
        return $this->loadRouteCollectionFromFiles([
            __DIR__.'/routes.xml',
            __DIR__.'/routes.yml',
        ]);
    }
}
