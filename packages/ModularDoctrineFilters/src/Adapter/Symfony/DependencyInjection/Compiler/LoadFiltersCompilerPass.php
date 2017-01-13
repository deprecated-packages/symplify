<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionFinder;

final class LoadFiltersCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const NAME_CONFIGURATION = 'doctrine.orm.default_configuration';

    /**
     * @var string[]
     */
    private $newFilters = [];

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function process(ContainerBuilder $containerBuilder) : void
    {
        $this->containerBuilder = $containerBuilder;
        $this->addFiltersToOrmConfiguration();
    }

    private function addFiltersToOrmConfiguration() : void
    {
        $defaultOrmConfiguration = $this->containerBuilder->getDefinition(self::NAME_CONFIGURATION);
        $filterManager = $this->containerBuilder->getDefinition('symplify.filter_manager');
        $filterManager->setAutowired(true);

        foreach ($this->getFiltersDefinitions() as $name => $definition) {
            $definition->setAutowired(true);

            // 1) load to Doctrine
            $defaultOrmConfiguration->addMethodCall('addFilter', [$name, $definition->getClass()]);
            $this->newFilters[] = $name;

            // 2) load to FilterManager to run conditions and enable allowed only
            $filterManager->addMethodCall('addFilter', [$name, new Reference($name)]);
        }

        $this->passFilterManagerToListener();
    }

    /**
     * Prevents circular reference.
     */
    private function passFilterManagerToListener() : void
    {
        $this->containerBuilder->getDefinition('symplify.enable_filters_subscriber')
            ->addMethodCall('setFilterManager', [new Reference('symplify.filter_manager')]);
    }

    /**
     * @return Definition[]
     */
    private function getFiltersDefinitions() : array
    {
        return DefinitionFinder::findAllByType($this->containerBuilder, FilterInterface::class);
    }
}
