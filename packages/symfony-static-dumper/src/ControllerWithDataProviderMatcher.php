<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper;

use Symfony\Component\Routing\Route;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use Symplify\SymfonyStaticDumper\Routing\ControllerMatcher;

final class ControllerWithDataProviderMatcher
{
    /**
     * @param ControllerWithDataProviderInterface[] $controllerWithDataProviders
     */
    public function __construct(
        private ControllerMatcher $controllerMatcher,
        private array $controllerWithDataProviders
    ) {
    }

    public function matchRoute(Route $route): ?ControllerWithDataProviderInterface
    {
        $controllerCallable = $this->controllerMatcher->matchRouteToControllerAndMethod($route);

        foreach ($this->controllerWithDataProviders as $controllerWithDataProvider) {
            if ($controllerWithDataProvider->getControllerClass() !== $controllerCallable->getClass()) {
                continue;
            }

            if ($controllerWithDataProvider->getControllerMethod() !== $controllerCallable->getMethod()) {
                continue;
            }

            return $controllerWithDataProvider;
        }

        return null;
    }
}
