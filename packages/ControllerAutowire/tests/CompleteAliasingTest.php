<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;
use Symplify\ControllerAutowire\Tests\AliasingBundle\Controller\AliasController;
use Symplify\ControllerAutowire\Tests\CompleteTestSource\Scan\ContainerAwareController;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeController;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeService;

final class CompleteAliasingTest extends TestCase
{
    /**
     * @var ControllerResolver
     */
    private $controllerResolver;

    protected function setUp()
    {
        $kernel = new AppKernelWithAlias('another_env', true);
        $kernel->boot();

        $this->controllerResolver = $kernel->getContainer()
            ->get('default.controller_resolver');
    }

    public function testControllerResolver()
    {
        $this->assertInstanceOf(ControllerResolver::class, $this->controllerResolver);
    }

    public function testGetAutowiredController()
    {
        $request = new Request();
        $request->attributes->set('_controller', SomeController::class . '::someAction');

        /** @var SomeController $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(SomeController::class, $controller);
        $this->assertInstanceOf(SomeService::class, $controller->getSomeService());
    }

    public function testGetContainerAwareController()
    {
        $request = new Request();
        $request->attributes->set('_controller', ContainerAwareController::class . '::someAction');

        /** @var ContainerAwareController $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(ContainerAwareController::class, $controller);
        $this->assertInstanceOf(ContainerInterface::class, $controller->getContainer());
    }

    public function testGetControllerServiceMissing()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'some.missing.controller.service:someAction');

        $controller = $this->controllerResolver->getController($request);
        $this->assertNull($controller);
    }

    public function testGetControllerAliasConfig()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'AliasingBundle:Alias:some');

        $controller = $this->controllerResolver->getController($request)[0];
        $this->assertInstanceOf(AliasController::class, $controller);
    }
}
