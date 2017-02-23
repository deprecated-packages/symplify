<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symplify\ControllerAutowire\Controller\ControllerTrait;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;
use Symplify\ControllerAutowire\Tests\CompleteTestSource\Controller\ControllerWithParameter;
use Symplify\ControllerAutowire\Tests\CompleteTestSource\DoNotScan\SomeRegisteredController;
use Symplify\ControllerAutowire\Tests\CompleteTestSource\Scan\ContainerAwareController;
use Symplify\ControllerAutowire\Tests\CompleteTestSource\Scan\TraitAwareController;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeController;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeService;

final class CompleteTest extends TestCase
{
    /**
     * @var ControllerResolver
     */
    private $controllerResolver;

    protected function setUp(): void
    {
        $kernel = new AppKernel;
        $kernel->boot();

        $this->controllerResolver = $kernel->getContainer()
            ->get('symplify.controller_resolver');
    }

    public function testMissingControllerParameter(): void
    {
        $request = new Request;
        $this->assertFalse($this->controllerResolver->getController($request));
    }

    public function testGetAutowiredController(): void
    {
        $request = new Request;
        $request->attributes->set('_controller', SomeController::class . '::someAction');

        /** @var SomeController $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(SomeController::class, $controller);
        $this->assertInstanceOf(SomeService::class, $controller->getSomeService());
    }

    public function testGetContainerAwareController(): void
    {
        $request = new Request;
        $request->attributes->set('_controller', ContainerAwareController::class . '::someAction');

        /** @var ContainerAwareController $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(ContainerAwareController::class, $controller);
        $this->assertInstanceOf(ContainerInterface::class, $controller->getContainer());
    }

    public function testGetAutowiredControllerWithParameter(): void
    {
        $request = new Request;
        $request->attributes->set('_controller', 'some.controller.with_parameter:someAction');

        /** @var ControllerWithParameter $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(ControllerWithParameter::class, $controller);
        $this->assertSame(__DIR__, $controller->getKernelRootDir());
    }

    public function testGetControllerWithTrait(): void
    {
        $request = new Request;
        $request->attributes->set(
            '_controller',
            'symplify.controllerautowire.tests.completetestsource.scan.traitawarecontroller:someAction'
        );

        /** @var TraitAwareController|ControllerTrait $controller */
        $controller = $this->controllerResolver->getController($request)[0];

        $this->assertInstanceOf(TraitAwareController::class, $controller);

        $httpKernel = Assert::getObjectAttribute($controller, 'httpKernel');
        $this->assertInstanceOf(HttpKernelInterface::class, $httpKernel);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testGetControllerServiceMissing(): void
    {
        $request = new Request;
        $request->attributes->set('_controller', 'some.missing.controller.service:someAction');

        $this->controllerResolver->getController($request);
    }

    public function testGetControllerServiceRegisteredInConfig(): void
    {
        $request = new Request;
        $request->attributes->set('_controller', 'some.controller.service:someAction');

        $controller = $this->controllerResolver->getController($request)[0];
        $this->assertInstanceOf(SomeRegisteredController::class, $controller);
    }
}
