<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\HttpKernel\Controller;

use PHPUnit\Framework\TestCase;
use Symplify\ControllerAutowire\Contract\HttpKernel\ControllerFinderInterface;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerFinder;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeController;
use Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource\SomeOtherController;

final class ControllerFinderTest extends TestCase
{
    /**
     * @var ControllerFinderInterface
     */
    private $controllerFinder;

    protected function setUp()
    {
        $this->controllerFinder = new ControllerFinder();
    }

    public function testFindControllersInDirs()
    {
        $controllers = $this->controllerFinder->findControllersInDirs([__DIR__.'/ControllerFinderSource']);

        $this->assertEquals(
            [SomeController::class, SomeOtherController::class],
            $controllers,
            '',
            0.0,
            10,
            true
        );
    }
}
