<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Routing;

use Nette\Utils\Strings;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symplify\SymfonyRouteUsage\EntityRepository\RouteVisitRepository;

/**
 * @see \Symplify\SymfonyRouteUsage\Tests\Routing\DeadRoutesProviderTest
 */
final class DeadRoutesProvider
{
    /**
     * @var RouteVisitRepository
     */
    private $routeVisitRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouteVisitRepository $routeVisitRepository, RouterInterface $router)
    {
        $this->routeVisitRepository = $routeVisitRepository;
        $this->router = $router;
    }

    /**
     * @return Route[]
     */
    public function provide(): array
    {
        $deadRoutes = [];
        foreach ($this->router->getRouteCollection() as $routeName => $routeCollection) {
            if ($this->isRouteUsed($routeName)) {
                continue;
            }

            if ($this->shouldSkipRoute($routeCollection)) {
                continue;
            }

            $deadRoutes[$routeName] = $routeCollection;
        }

        ksort($deadRoutes);

        return $deadRoutes;
    }

    private function isRouteUsed(string $routeName): bool
    {
        foreach ($this->routeVisitRepository->fetchAll() as $routeVisit) {
            if ($routeVisit->getRoute() === $routeName) {
                return true;
            }
        }

        return false;
    }

    private function shouldSkipRoute(Route $route): bool
    {
        $defaultController = $route->getDefault('_controller');
        if ($defaultController === RedirectController::class) {
            return true;
        }

        if (Strings::startsWith($defaultController, 'web_profiler')) {
            return true;
        }

        $path = $route->getPath();
        return Strings::startsWith($path, '/admin');
    }
}
