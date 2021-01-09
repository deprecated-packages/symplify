<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Routing;

use Symfony\Component\HttpFoundation\Request;

final class RouteHashFactory
{
    public function createFromRequest(Request $request): string
    {
        $route = (string) $request->get('_route');
        $method = $request->getMethod();

        return sha1($route . '_' . $method);
    }
}
