<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;
use Symplify\ControllerAutowire\Tests\AliasingBundle\Controller\ControllerWithParameter;
use Symplify\ControllerAutowire\Tests\CompleteTestSource\DoNotScan\SomeRegisteredController;
use Symplify\ControllerAutowire\Tests\CompleteTestSource\Scan\ContainerAwareController;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeController;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeService;

final class CompleteTest extends TestCase
{
    /**
     * @var ControllerResolver
     */
    private $controllerResolver;

    protected function setUp()
    {
        $kernel = new AppKernel('test_env', true);
        $kernel->boot();

        $this->controllerResolver = $kernel->getContainer()
            ->get('default.controller_resolver');
    }

    public function testMissingControllerParameter()
    {
        $request = new Request();
        $this->assertFalse($this->controllerResolver->getController($request));
    }

    public function testGetAutowiredController()
    {
        $request = new Request();
        $request->attributes->set('_controller', SomeController::class.'::someAction');

        /** @var SomeController $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(SomeController::class, $controller);
        $this->assertInstanceOf(SomeService::class, $controller->getSomeService());
    }

    public function testGetContainerAwareController()
    {
        $request = new Request();
        $request->attributes->set('_controller', ContainerAwareController::class.'::someAction');

        /** @var ContainerAwareController $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(ContainerAwareController::class, $controller);
        $this->assertInstanceOf(ContainerInterface::class, $controller->getContainer());
    }

    public function testGetAutowiredControllerWithParameter()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'some.controller.with_parameter:someAction');

        /** @var ControllerWithParameter $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(ControllerWithParameter::class, $controller);
        $this->assertSame(__DIR__, $controller->getKernelRootDir());
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testGetControllerServiceMissing()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'some.missing.controller.service:someAction');

        $this->controllerResolver->getController($request);
    }

    public function testGetControllerServiceRegisteredInConfig()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'some.controller.service:someAction');

        $controller = $this->controllerResolver->getController($request)[0];
        $this->assertInstanceOf(SomeRegisteredController::class, $controller);
    }
}
