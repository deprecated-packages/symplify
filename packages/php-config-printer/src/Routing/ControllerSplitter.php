<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Routing;

use Symplify\PhpConfigPrinter\Enum\RouteOption;
use Symplify\PhpConfigPrinter\ValueObject\Routing\RouteDefaults;

final class ControllerSplitter
{
    /**
     * @return string[]|string
     */
    public function splitControllerClassAndMethod(string $controllerValue): array|string
    {
        if (! str_contains($controllerValue, '::')) {
            return $controllerValue;
        }

        return explode('::', $controllerValue);
    }

    public function hasControllerDefaults(string $nestedKey, mixed $nestedValues): bool
    {
        if ($nestedKey !== RouteOption::DEFAULTS) {
            return false;
        }

        return array_key_exists(RouteDefaults::CONTROLLER, $nestedValues);
    }
}
