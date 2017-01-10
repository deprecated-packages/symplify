<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Config\Definition;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\DefaultAutowire\Config\Definition\ConfigurationResolver;
use Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle;

final class ConfigurationResolverTest extends TestCase
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    protected function setUp()
    {
        $this->configurationResolver = new ConfigurationResolver;
    }

    public function testResolveDefaults()
    {
        $resolvedConfiguration = $this->configurationResolver->resolveFromContainerBuilder(new ContainerBuilder);

        $this->assertCount(6, $resolvedConfiguration['autowire_types']);
        $this->assertSame([
            'autowire_types' => [
                'Doctrine\ORM\EntityManager' => 'doctrine.orm.default_entity_manager',
                'Doctrine\ORM\EntityManagerInterface' => 'doctrine.orm.default_entity_manager',
                'Doctrine\Portability\Connection' => 'database_connection',
                'Symfony\Component\EventDispatcher\EventDispatcher' => 'event_dispatcher',
                'Symfony\Component\EventDispatcher\EventDispatcherInterface' => 'event_dispatcher',
                'Symfony\Component\Translation\TranslatorInterface' => 'translator',
            ],
        ], $resolvedConfiguration);
    }

    public function testOverrideDefaults()
    {
        $containerBuilder = new ContainerBuilder;

        $containerBuilder->prependExtensionConfig(SymplifyDefaultAutowireBundle::ALIAS, [
            'autowire_types' => [
                'Doctrine\ORM\EntityManager' => 'other_entity_manager',
            ],
        ]);

        $resolvedConfiguration = $this->configurationResolver->resolveFromContainerBuilder(
            $containerBuilder
        );

        $autowireTypes = $resolvedConfiguration['autowire_types'];
        $this->assertCount(6, $autowireTypes);
        $this->assertSame('other_entity_manager', $autowireTypes['Doctrine\ORM\EntityManager']);
    }

    public function testAddNewValues()
    {
        $containerBuilder = new ContainerBuilder;

        $containerBuilder->prependExtensionConfig(SymplifyDefaultAutowireBundle::ALIAS, [
            'autowire_types' => [
                'SomeInterface' => 'some_service',
            ],
        ]);

        $resolvedConfiguration = $this->configurationResolver->resolveFromContainerBuilder(
            $containerBuilder
        );

        $autowireTypes = $resolvedConfiguration['autowire_types'];
        $this->assertCount(7, $autowireTypes);
        $this->assertSame('some_service', $autowireTypes['SomeInterface']);
    }

    public function testCache()
    {
        $resolvedConfiguration = $this->configurationResolver->resolveFromContainerBuilder(new ContainerBuilder);
        $resolvedConfiguration2 = $this->configurationResolver->resolveFromContainerBuilder(new ContainerBuilder);
        $this->assertSame($resolvedConfiguration, $resolvedConfiguration2);
    }
}
