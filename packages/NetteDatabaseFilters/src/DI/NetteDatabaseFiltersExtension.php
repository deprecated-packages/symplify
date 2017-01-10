<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\DI;

use Nette\Database\Context;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Zenify\NetteDatabaseFilters\Contract\FilterInterface;
use Zenify\NetteDatabaseFilters\Contract\FilterManagerInterface;
use Zenify\NetteDatabaseFilters\Database\FiltersAwareContext;
use Zenify\NetteDatabaseFilters\Sql\SqlParser;

final class NetteDatabaseFiltersExtension extends CompilerExtension
{
    public function loadConfiguration() : void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
        );
    }

    public function beforeCompile() : void
    {
        $this->replaceContextWithOwnClass();
        $this->setFilterManagerToContexts();
        $this->collectFiltersToFilterManager();
    }

    public function replaceContextWithOwnClass() : void
    {
        foreach ($this->getContainerBuilder()->findByType(Context::class) as $contextDefinition) {
            $contextDefinition->setFactory(FiltersAwareContext::class);
        }
    }

    private function setFilterManagerToContexts() : void
    {
        $filterManagerDefinition = $this->getDefinitionByType(FilterManagerInterface::class);

        foreach ($this->getContainerBuilder()->findByType(Context::class) as $contextDefinition) {
            $contextDefinition->setFactory(FiltersAwareContext::class);
            $contextDefinition->addSetup('setFilterManager', ['@' . $filterManagerDefinition->getClass()]);
            $contextDefinition->addSetup('setSqlParser', ['@' . SqlParser::class]);
        }
    }

    private function collectFiltersToFilterManager() : void
    {
        $filterManagerDefinition = $this->getDefinitionByType(FilterManagerInterface::class);

        foreach ($this->getContainerBuilder()->findByType(FilterInterface::class) as $name => $definition) {
            $filterManagerDefinition->addSetup('addFilter', ['@' . $name]);
        }
    }

    private function getDefinitionByType(string $type) : ServiceDefinition
    {
        $definitionsByType = $this->getContainerBuilder()
            ->findByType($type);

        return array_pop($definitionsByType);
    }
}
