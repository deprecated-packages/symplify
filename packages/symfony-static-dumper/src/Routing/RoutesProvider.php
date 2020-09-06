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
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return Route[]
     */
    public function provide(): array
    {
        return $this->router->getRouteCollection()->all();
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
        return (bool) Strings::match($route->getPath(), '#\{(.*?)\}#sm');
    }
}
