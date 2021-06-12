<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Routing;

use Nette\Utils\Strings;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @see \Symplify\SymfonyStaticDumper\Tests\Routing\RoutesProviderTest
 */
final class RoutesProvider
{
    /**
     * @var string
     * @see https://regex101.com/r/VxkiVa/1
     */
    private const PARAMETERS_IN_ROUTE_REGEX = '#\{(.*?)\}#sm';

    public function __construct(
        private RouterInterface $router
    ) {
    }

    /**
     * @return Route[]
     */
    public function provide(): array
    {
        $routeCollection = $this->router->getRouteCollection();
        return $routeCollection->all();
    }

    /**
     * @return Route[]
     */
    public function provideRoutesWithoutArguments(): array
    {
        return array_filter($this->provide(), function (Route $route): bool {
            return ! $this->hasRouteParameters($route);
        });
    }

    /**
     * @return Route[]
     */
    public function provideRoutesWithParameters(): array
    {
        return array_filter($this->provide(), function (Route $route): bool {
            return $this->hasRouteParameters($route);
        });
    }

    private function hasRouteParameters(Route $route): bool
    {
        return (bool) Strings::match($route->getPath(), self::PARAMETERS_IN_ROUTE_REGEX);
    }
}
