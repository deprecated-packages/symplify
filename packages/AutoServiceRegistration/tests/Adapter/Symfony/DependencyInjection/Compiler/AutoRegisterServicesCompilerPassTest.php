<?php

declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\Adapter\Symfony\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\AutoServiceRegistration\Adapter\Symfony\Config\Definition\Configuration;
use Symplify\AutoServiceRegistration\Adapter\Symfony\DependencyInjection\Compiler\AutoRegisterServicesCompilerPass;
use Symplify\AutoServiceRegistration\Adapter\Symfony\SymplifyAutoServiceRegistrationBundle;
use Symplify\AutoServiceRegistration\ServiceClass\ServiceClassFinder;
use Symplify\AutoServiceRegistration\Tests\Adapter\Symfony\Source\SomeController;
use Symplify\AutoServiceRegistration\Tests\Adapter\Symfony\Source\SomeRepository;

final class AutoRegisterServicesCompilerPassTest extends TestCase
{
    /**
     * @var AutoRegisterServicesCompilerPass()
     */
    private $autoRegisterServicesCompilerPass;

    protected function setUp()
    {
        $this->autoRegisterServicesCompilerPass = new AutoRegisterServicesCompilerPass(new ServiceClassFinder());
    }

    /**
     * @dataProvider provideData()
     */
    public function testProcess(array $directories, array $classSuffixes, int $expectedCount, string $expectedClassType)
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->prependExtensionConfig(SymplifyAutoServiceRegistrationBundle::ALIAS, [
            Configuration::DIRECTORIES_TO_SCAN => $directories,
            Configuration::CLASS_SUFFIXES_TO_SEEK => $classSuffixes,
        ]);
        $this->autoRegisterServicesCompilerPass->process($containerBuilder);

        $definitions = $containerBuilder->getDefinitions();
        $this->assertCount($expectedCount, $definitions);

        /** @var Definition $controllerDefinition */
        $controllerDefinition = array_pop($definitions);

        $this->assertSame($expectedClassType, $controllerDefinition->getClass());
        $this->assertTrue($controllerDefinition->isAutowired());
    }

    public function provideData() : array
    {
        return [
            [
                [__DIR__ . '/../../Source'],
                ['Controller'],
                1,
                SomeController::class,
            ],
            [
                [__DIR__ . '/../../Source'],
                ['Repository'],
                1,
                SomeRepository::class,
            ],
        ];
    }
}
