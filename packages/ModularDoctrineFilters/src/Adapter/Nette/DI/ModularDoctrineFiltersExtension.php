<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Nette\DI;

use Doctrine\ORM\Configuration;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\EventSubscriber\EnableFiltersSubscriber;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionFinder;

final class ModularDoctrineFiltersExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../../../config/services.neon')['services']
        );
    }

    public function beforeCompile() : void
    {
        $this->loadFiltersToFilterManager();
        $this->passFilterManagerToSubscriber();
    }

    private function loadFiltersToFilterManager() : void
    {
        $containerBuilder = $this->getContainerBuilder();

        $filterManagerDefinition = $containerBuilder->getDefinitionByType(FilterManagerInterface::class);
        $ormConfigurationDefinition = DefinitionFinder::getByType($containerBuilder, Configuration::class);

        $filterDefinitions = $containerBuilder->findByType(FilterInterface::class);
        foreach ($filterDefinitions as $name => $filterDefinition) {
            // 1) to filter manager to run conditions and enable allowed only
            $filterManagerDefinition->addSetup(
                'addFilter',
                [$name, '@' . $name]
            );
            // 2) to Doctrine itself
            $ormConfigurationDefinition->addSetup(
                'addFilter',
                [$name, $filterDefinition->getClass()]
            );
        }
    }

    /**
     * Prevents circular reference.
     */
    private function passFilterManagerToSubscriber() : void
    {
        $enableFiltersSubscriberDefinition = $this->getContainerBuilder()
            ->getDefinitionByType(EnableFiltersSubscriber::class);

        $filterManagerServiceName = $this->getContainerBuilder()
            ->getByType(FilterManagerInterface::class);

        $enableFiltersSubscriberDefinition->addSetup(
            'setFilterManager',
            ['@' . $filterManagerServiceName]
        );
    }
}
