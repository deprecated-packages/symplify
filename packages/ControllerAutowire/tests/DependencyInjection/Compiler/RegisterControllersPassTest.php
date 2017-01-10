<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\RegisterControllersPass;
use Symplify\ControllerAutowire\DependencyInjection\ControllerClassMap;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerFinder;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;
use Symplify\ControllerAutowire\Tests\DependencyInjection\Compiler\RegisterControllersPassSource\SomeController;

final class RegisterControllersPassTest extends TestCase
{
    /**
     * @var RegisterControllersPass
     */
    private $registerControllersPass;

    protected function setUp()
    {
        $controllerClassMap = new ControllerClassMap;
        $controllerClassMap->addController('somecontroller', 'SomeController');

        $controllerFinder = new ControllerFinder;
        $this->registerControllersPass = new RegisterControllersPass($controllerClassMap, $controllerFinder);
    }

    public function testProcess()
    {
        $containerBuilder = new ContainerBuilder;
        $this->assertCount(0, $containerBuilder->getDefinitions());

        $containerBuilder->prependExtensionConfig(SymplifyControllerAutowireBundle::ALIAS, [
            'controller_dirs' => [
                __DIR__ . '/RegisterControllersPassSource',
            ],
        ]);
        $this->registerControllersPass->process($containerBuilder);

        $definitions = $containerBuilder->getDefinitions();
        $this->assertCount(1, $definitions);

        /** @var Definition $controllerDefinition */
        $controllerDefinition = array_pop($definitions);
        $this->assertInstanceOf(Definition::class, $controllerDefinition);

        $this->assertSame(SomeController::class, $controllerDefinition->getClass());
        $this->assertTrue($controllerDefinition->isAutowired());
    }

    public function testServiceDefinitionExists()
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->prependExtensionConfig(SymplifyControllerAutowireBundle::ALIAS, [
            'controller_dirs' => [
                __DIR__ . '/RegisterControllersPassSource',
            ],
        ]);

        $controllerDefition = new Definition(SomeController::class);
        $containerBuilder->setDefinition(
            'symplify.controllerautowire.tests.dependencyinjection.'
                . 'compiler.registercontrollerspasssource.somecontroller',
            $controllerDefition
        );
        $this->assertCount(1, $containerBuilder->getDefinitions());

        $this->registerControllersPass->process($containerBuilder);
        $this->assertCount(1, $containerBuilder->getDefinitions());

        $this->assertTrue($controllerDefition->isAutowired());
    }
}
