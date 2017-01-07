<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\HttpKernel\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

final class ControllerResolver implements ControllerResolverInterface
{
    /**
     * @var ControllerResolverInterface
     */
    private $controllerResolver;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ControllerNameParser
     */
    private $controllerNameParser;

    /**
     * @var string[]
     */
    private $controllerClassMap;

    public function __construct(
        ControllerResolverInterface $controllerResolver,
        ContainerInterface $container,
        ControllerNameParser $controllerNameParser
    ) {
        $this->controllerResolver = $controllerResolver;
        $this->container = $container;
        $this->controllerNameParser = $controllerNameParser;
    }

    public function setControllerClassMap(array $controllerClassMap) : void
    {
        $this->controllerClassMap = array_flip($controllerClassMap);
    }

    /**
     * @return false|array|callable
     */
    public function getController(Request $request)
    {
        if (! $controllerName = $request->attributes->get('_controller')) {
            return false;
        }

        [$class, $method] = $this->splitControllerClassAndMethod($controllerName);

        if (! isset($this->controllerClassMap[$class])) {
            return $this->controllerResolver->getController($request);
        }

        $controller = $this->getControllerService($class);
        $controller = $this->decorateControllerWithContainer($controller);

        return [$controller, $method];
    }

    /**
<<<<<<< 5cd59f9784c144a7a320f3072d016ce771655456
     * @param Request $request
     * @param callable $controller
     * @return array|mixed
=======
     * @return array|null
>>>>>>> drop inheritdoc, no info value
     */
    public function getArguments(Request $request, $controller)
    {
        return $this->controllerResolver->getArguments($request, $controller);
    }

    /**
     * @return object
     */
    private function getControllerService(string $class)
    {
        $serviceName = $this->controllerClassMap[$class];

        return $this->container->get($serviceName);
    }

    /**
     * @param object $controller
     *
     * @return object
     */
    private function decorateControllerWithContainer($controller)
    {
        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return $controller;
    }

    /**
     * @return string[]
     */
    private function splitControllerClassAndMethod(string $controllerName) : array
    {
        if (strpos($controllerName, '::') !== false) {
            return explode('::', $controllerName, 2);
        } elseif (substr_count($controllerName, ':') === 2) {
            $controllerName = $this->controllerNameParser->parse($controllerName);

            return explode('::', $controllerName, 2);
        } elseif (strpos($controllerName, ':') !== false) {
            return explode(':', $controllerName, 2);
        }
    }
}
