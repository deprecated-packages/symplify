<?php

declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\DefaultAutowire\DependencyInjection\Definition\DefinitionAnalyzer;
use Symplify\DefaultAutowire\DependencyInjection\Definition\DefinitionValidator;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\
    MissingArgumentsTypehintsFactory;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\
    NotMissingArgumentsTypehintsFactory;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\EmptyConstructor;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\EmptyConstructorFactory;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\MissingArgumentsTypehints;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\NotMissingArgumentsTypehints;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\TestEntity;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\TestEntityRepository;

final class DefinitionAnalyzerForFactoryTest extends TestCase
{
    /**
     * @var DefinitionAnalyzer
     */
    private $definitionAnalyzer;

    protected function setUp()
    {
        $this->definitionAnalyzer = new DefinitionAnalyzer(new DefinitionValidator());
    }

    public function testServiceFactoryMethodDoesNotHaveArguments()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'factory' => new Definition(EmptyConstructorFactory::class),
        ]);

        $definition = new Definition(EmptyConstructor::class);
        $definition->setFactory([
            new Reference('factory'),
            'create',
        ]);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $definition));
    }

    public function testFactoryMethodDoesNotHaveArguments()
    {
        $definition = new Definition(EmptyConstructor::class);
        $definition->setFactory([
            EmptyConstructorFactory::class,
            'create',
        ]);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder(), $definition));
    }

    public function testServiceFactoryBuiltClassHaveMissingArgumentsTypehints()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'factory' => new Definition(MissingArgumentsTypehintsFactory::class),
        ]);

        $definition = new Definition(MissingArgumentsTypehints::class);
        $definition->setFactory([
            new Reference('factory'),
            'create',
        ]);

        $definition->setArguments(['@someService']);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $definition));
    }

    public function testFactoryBuiltClassHaveMissingArgumentsTypehints()
    {
        $definition = new Definition(MissingArgumentsTypehints::class);
        $definition->setFactory([
            MissingArgumentsTypehintsFactory::class,
            'create',
        ]);

        $definition->setArguments(['@someService']);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder(), $definition));
    }

    public function testServiceFactoryBuiltClassHaveNotMissingArgumentsTypehints()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'factory' => new Definition(NotMissingArgumentsTypehintsFactory::class),
        ]);

        $definition = new Definition(NotMissingArgumentsTypehints::class);
        $definition->setFactory([
            new Reference('factory'),
            'create',
        ]);

        $definition->setArguments(['@someService']);

        $this->assertTrue($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $definition));
    }

    public function testFactoryBuiltClassHaveNotMissingArgumentsTypehints()
    {
        $definition = new Definition(NotMissingArgumentsTypehints::class);
        $definition->setFactory([
            NotMissingArgumentsTypehintsFactory::class,
            'create',
        ]);

        $definition->setArguments(['@someService']);

        $this->assertTrue($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder(), $definition));
    }

    public function testFactoryServiceIsUsedByAlias()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'factory' => new Definition(EmptyConstructorFactory::class),
        ]);
        $containerBuilder->addAliases([
            'factory_alias' => 'factory',
        ]);

        $definition = new Definition(EmptyConstructor::class);
        $definition->setFactory([
            new Reference('factory_alias'),
            'create',
        ]);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $definition));
    }

    public function testFactoryServiceCanBeDecorated()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'factory' => new Definition(EmptyConstructorFactory::class),
            'decorated_factory' => new DefinitionDecorator('factory'),
        ]);

        $definition = new Definition(EmptyConstructor::class);
        $definition->setFactory([
            new Reference('decorated_factory'),
            'create',
        ]);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $definition));
    }

    public function testFactoryClassNameIsDefinedByParameter()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'factory' => new Definition('%factory_class_param%'),
        ]);
        $containerBuilder->setParameter('factory_class_param', EmptyConstructorFactory::class);

        $definition = new Definition(EmptyConstructor::class);
        $definition->setFactory([
            new Reference('factory'),
            'create',
        ]);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $definition));
    }

    public function testDoctrineRepositoryAsService()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'doctrine.orm.default_entity_manager' => new DefinitionDecorator('doctrine.orm.entity_manager.abstract'),
            'doctrine.orm.entity_manager.abstract' => new Definition('%doctrine.orm.entity_manager.class%'),
        ]);
        $containerBuilder->setAlias('doctrine.orm.entity_manager', new Alias('doctrine.orm.default_entity_manager'));
        $containerBuilder->setParameter('doctrine.orm.entity_manager.class', EntityManager::class);

        $testRepository = new Definition(TestEntityRepository::class);
        $testRepository->setFactory([
            new Reference('doctrine.orm.entity_manager'),
            'getRepository',
        ]);
        $testRepository->addArgument(TestEntity::class);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $testRepository));
    }
}
