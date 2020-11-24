<?php

declare(strict_types=1);

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symplify\SymfonyRouteUsage\Tests\Routing\RouterFactory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/packages/*');

    $parameters = $containerConfigurator->parameters();
    $parameters->set('database_name', 'migrify_symfony_route_usage_tests');

    $services = $containerConfigurator->services();
    $services->set(Psr16Cache::class);
    $services->alias(CacheInterface::class, Psr16Cache::class);

    $services->set(Security::class)
        ->args([ref('service_container')]);

    $services->set(RouterFactory::class);
    $services->set(Router::class)
        ->factory([ref(RouterFactory::class), 'create']);
    $services->alias(RouterInterface::class, Router::class);
};
