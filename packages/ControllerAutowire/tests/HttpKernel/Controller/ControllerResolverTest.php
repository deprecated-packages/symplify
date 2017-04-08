<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\HttpKernel\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerResolver;

final class ControllerResolverTest extends TestCase
{
    /**
     * @var ControllerResolver
     */
    private $controllerResolver;

    protected function setUp(): void
    {
        $this->controllerResolver = $this->createControllerResolverWithMocks();
    }

    public function testGetController(): void
    {
        $attributes = [
            '_controller' => 'SomeController::someAction'
        ];
        $request = new Request([], [], $attributes);

        $controller = $this->controllerResolver->getController($request);
        $this->assertNull($controller);
    }

    public function testGetArguments(): void
    {
        $this->assertNull(
            $this->controllerResolver->getArguments(new Request, 'missing')
        );
    }

    private function createControllerResolverWithMocks(): ControllerResolver
    {
        $parentControllerResolverMock = $this->prophesize(ControllerResolverInterface::class);
        $containerMock = $this->prophesize(ContainerInterface::class);
        $controllerNameParser = $this->prophesize(ControllerNameParser::class);

        return new ControllerResolver(
            $parentControllerResolverMock->reveal(),
            $containerMock->reveal(),
            $controllerNameParser->reveal()
        );
    }
}
