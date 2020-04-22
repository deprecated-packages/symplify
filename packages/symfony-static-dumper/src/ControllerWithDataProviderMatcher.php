<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper;

use Symfony\Component\Routing\Route;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use Symplify\SymfonyStaticDumper\Routing\ControllerMatcher;

final class ControllerWithDataProviderMatcher
{
    /**
     * @var ControllerWithDataProviderInterface[]
     */
    private $controllerWithDataProviders = [];

    /**
     * @var ControllerMatcher
     */
    private $controllerMatcher;

    /**
     * @param ControllerWithDataProviderInterface[] $controllerWithDataProviders
     */
    public function __construct(ControllerMatcher $controllerMatcher, array $controllerWithDataProviders)
    {
        $this->controllerMatcher = $controllerMatcher;
        $this->controllerWithDataProviders = $controllerWithDataProviders;
    }

    public function matchRoute(Route $route): ?ControllerWithDataProviderInterface
    {
        [$controllerClass, $method] = $this->controllerMatcher->matchRouteToControllerAndMethod($route);

        foreach ($this->controllerWithDataProviders as $controllerWithDataProvider) {
            if ($controllerWithDataProvider->getControllerClass() !== $controllerClass) {
                continue;
            }

            if ($controllerWithDataProvider->getControllerMethod() !== $method) {
                continue;
            }

            return $controllerWithDataProvider;
        }

        return null;
    }
}
