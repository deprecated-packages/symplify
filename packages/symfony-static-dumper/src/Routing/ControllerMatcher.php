<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Routing;

use Nette\Utils\Strings;
use Symfony\Component\Routing\Route;

final class ControllerMatcher
{
    /**
     * @return string[]
     */
    public function matchRouteToControllerAndMethod(Route $route): array
    {
        $controller = $route->getDefault('_controller');

        if (Strings::contains($controller, '::')) {
            [$controllerClass, $method] = Strings::split($controller, '#::#');
        } else {
            $controllerClass = $controller;
            $method = '__invoke';
        }

        return [$controllerClass, $method];
    }
}
