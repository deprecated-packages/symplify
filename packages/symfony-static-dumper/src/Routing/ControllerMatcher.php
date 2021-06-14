<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Routing;

use Nette\Utils\Strings;
use Symfony\Component\Routing\Route;
use Symplify\SymfonyStaticDumper\ValueObject\ControllerCallable;

final class ControllerMatcher
{
    /**
     * @var string
     * @see https://regex101.com/r/TiQ1x8/1
     */
    private const DOUBLE_COLLON_REGEX = '#::#';

    public function matchRouteToControllerAndMethod(Route $route): ControllerCallable
    {
        $controller = $route->getDefault('_controller');

        if (\str_contains($controller, '::')) {
            [$controllerClass, $method] = Strings::split($controller, self::DOUBLE_COLLON_REGEX);
        } else {
            $controllerClass = $controller;
            $method = '__invoke';
        }

        return new ControllerCallable($controllerClass, $method);
    }
}
