<?php declare(strict_types=1);

namespace Symplify\ActionAutowire\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symplify\ActionAutowire\DependencyInjection\ServiceLocator;
use Symplify\ActionAutowire\Tests\CompleteSource\SomeService;
use Symplify\ActionAutowire\Tests\Controller\SomeController;

final class CompleteTest extends TestCase
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var ControllerResolver
     */
    private $controllerResolver;

    protected function setUp(): void
    {
        $kernel = new AppKernel;
        $kernel->boot();

        $this->serviceLocator = $kernel->getContainer()
            ->get('symplify.action_autowire.service_locator');

        $this->controllerResolver = $kernel->getContainer()
            ->get('debug.controller_resolver');
    }

    public function testServiceLocator(): void
    {
        $this->assertInstanceOf(SomeService::class, $this->serviceLocator->getByType(SomeService::class));

        $this->assertFalse((bool) $this->serviceLocator->getByType('missing'));
    }

    public function testGetAutowiredControllerAction(): void
    {
        $request = new Request;
        $request->attributes->set('_controller', SomeController::class . '::someServiceAwareAction');

        /** @var callable $controller */
        $controller = $this->controllerResolver->getController($request);
        $arguments = $this->controllerResolver->getArguments($request, $controller);

        $this->assertInstanceOf(SomeService::class, $arguments[0]);
    }
}
