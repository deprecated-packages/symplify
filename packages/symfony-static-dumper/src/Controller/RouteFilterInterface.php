<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Controller;

use Symfony\Component\Routing\Route;

interface RouteFilterInterface
{
    /**
     * @param Route[] $routes
     * @return Route[]
     */
    public function filter(array $routes): array;
}
