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
     */
    private const DOUBLE_COLLON_REGEX = '#::#';

    public function matchRouteToControllerAndMethod(Route $route): ControllerCallable
    {
        $controller = $route->getDefault('_controller');

        if (Strings::contains($controller, '::')) {
            [$controllerClass, $method] = Strings::split($controller, self::DOUBLE_COLLON_REGEX);
        } else {
            $controllerClass = $controller;
            $method = '__invoke';
        }

        return new ControllerCallable($controllerClass, $method);
    }
}
