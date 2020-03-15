<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

final class RoutesProvider
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return Route[]
     */
    public function provide(): array
    {
        return $this->router->getRouteCollection()->all();
    }
}
