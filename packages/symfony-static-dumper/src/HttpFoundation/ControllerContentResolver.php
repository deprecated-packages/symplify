<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\HttpFoundation;

use Nette\Utils\Strings;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symplify\SymfonyStaticDumper\Routing\ControllerMatcher;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

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

    /**
     * @param mixed $values
     */
    public function resolveFromRouteAndArgument(string $routeName, Route $route, $values): ?string
    {
        $controllerCallable = $this->controllerMatcher->matchRouteToControllerAndMethod($route);
        if (! class_exists($controllerCallable->getClass())) {
            return null;
        }

        $controller = $this->container->get($controllerCallable->getClass());
        if (! is_object($controller)) {
            throw new ShouldNotHappenException();
        }

        $this->fakeRequest($routeName);

        if (! is_array($values)) {
            $values = [$values];
        }

        $reflectionMethod = new ReflectionMethod($controller, $controllerCallable->getMethod());
        /** @var Response $response */
        $response = $reflectionMethod->invokeArgs($controller, $values);

        return (string) $response->getContent();
    }

    public function resolveFromRoute(string $routeName, Route $route): ?string
    {
        $controllerCallable = $this->controllerMatcher->matchRouteToControllerAndMethod($route);
        if (! class_exists($controllerCallable->getClass())) {
            return null;
        }

        $controller = $this->container->get($controllerCallable->getClass());
        if (! is_object($controller)) {
            throw new ShouldNotHappenException();
        }

        $this->fakeRequest($routeName);

        $defaultParams = array_filter($route->getDefaults(), static function (string $key): bool {
            return ! Strings::startsWith($key, '_');
        }, ARRAY_FILTER_USE_KEY);

        $reflectionMethod = new ReflectionMethod($controller, $controllerCallable->getMethod());
        $defaultParamsValues = array_values($defaultParams);
        /** @var Response $response */
        $response = $reflectionMethod->invokeArgs($controller, $defaultParamsValues);

        return (string) $response->getContent();
    }

    private function fakeRequest(string $routeName): void
    {
        // fake the request
        $fakeRequest = new Request();
        $fakeRequest->attributes->set('_route', $routeName);

        $this->requestStack->pop();
        $this->requestStack->push($fakeRequest);
    }
}
