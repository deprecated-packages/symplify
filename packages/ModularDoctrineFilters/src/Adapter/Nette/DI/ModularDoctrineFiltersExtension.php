<?php declare(strict_types=1);

namespace Symplify\DoctrineFilters\Adapter\Nette\DI;

use Doctrine\ORM\Configuration;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\ModularDoctrineFilters\Adapter\Nette\DI\DefinitionFinder;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\EventSubscriber\EnableFiltersSubscriber;

final class ModularDoctrineFiltersExtension extends CompilerExtension
{
    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    public function loadConfiguration()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../../../config/services.neon')['services']
        );
    }

    public function beforeCompile()
    {
        $this->definitionFinder = new DefinitionFinder($this->getContainerBuilder());

        $this->loadFiltersToFilterManager();
        $this->passFilterManagerToSubscriber();
    }

    private function loadFiltersToFilterManager() : void
    {
        $containerBuilder = $this->getContainerBuilder();

        $filterManagerDefinition = $this->definitionFinder->getDefinitionByType(FilterManagerInterface::class);
        $ormConfigurationDefinition = $this->definitionFinder->getDefinitionByType(Configuration::class);

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
        $enableFiltersSubscriberDefinition = $this->definitionFinder->getDefinitionByType(
            EnableFiltersSubscriber::class
        );

        $filterManagerServiceName = $this->getContainerBuilder()
            ->getByType(FilterManagerInterface::class);

        $enableFiltersSubscriberDefinition->addSetup(
            'setFilterManager',
            ['@' . $filterManagerServiceName]
        );
    }
}
