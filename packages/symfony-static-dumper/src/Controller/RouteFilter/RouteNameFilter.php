<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Controller\RouteFilter;

use Symplify\SymfonyStaticDumper\Controller\RouteFilterInterface;
use function array_filter;
use function in_array;

final class RouteNameFilter implements RouteFilterInterface
{
    /**
     * @var string[]
     */
    private $routeNames;

    public function __construct(array $routeNames)
    {
        $this->routeNames = $routeNames;
    }

    public function filter(array $routes): array
    {
        return array_filter(
            $routes,
            function (string $name): bool {
                return in_array($name, $this->routeNames, true);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
