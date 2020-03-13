<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\HttpFoundation;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symplify\SymfonyStaticDumper\Exception\ShouldNotHappenException;
use Symplify\SymfonyStaticDumper\Routing\ControllerMatcher;

final class ControllerContentResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ControllerMatcher
     */
    private $controllerMatcher;

    public function __construct(
        ContainerInterface $container,
        RequestStack $requestStack,
        ControllerMatcher $controllerMatcher
    ) {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->controllerMatcher = $controllerMatcher;
    }

    public function resolveFromRouteAndArgument(Route $route, ...$values): ?string
    {
        [$controllerClass, $method] = $this->controllerMatcher->matchRouteToControllerAndMethod($route);
        if (! class_exists($controllerClass)) {
            return null;
        }

        $controller = $this->container->get($controllerClass);
        if (! is_object($controller)) {
            throw new ShouldNotHappenException();
        }

        $this->fakeRequest($route);

        /** @var Response $response */
        $response = call_user_func([$controller, $method], ...$values);

        return (string) $response->getContent();
    }

    public function resolveFromRoute(Route $route): ?string
    {
        [$controllerClass, $method] = $this->controllerMatcher->matchRouteToControllerAndMethod($route);
        if (! class_exists($controllerClass)) {
            return null;
        }

        $controller = $this->container->get($controllerClass);
        if (! is_object($controller)) {
            throw new ShouldNotHappenException();
        }

        $this->fakeRequest($route);

        /** @var Response $response */
        $response = call_user_func([$controller, $method]);

        return (string) $response->getContent();
    }

    private function fakeRequest(Route $route): void
    {
        // fake the request
        $fakeRequest = new Request();
        $fakeRequest->attributes->set('_route', $route->getPath());
        $this->requestStack->push($fakeRequest);
    }
}
