<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Controller\RouteFilter;

use Symplify\SymfonyStaticDumper\Controller\RouteFilterInterface;

final class WildcardFilter implements RouteFilterInterface
{
    public function filter(array $routes): array
    {
        return $routes;
    }
}
