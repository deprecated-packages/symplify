<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Tests\Routing;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

final class RouterFactory
{
    public function create(): RouterInterface
    {
        // simulate router
        $yamlFileLoader = new YamlFileLoader(new FileLocator(__DIR__ . '/../config'));
        return new Router($yamlFileLoader, 'routes.yaml');
    }
}
