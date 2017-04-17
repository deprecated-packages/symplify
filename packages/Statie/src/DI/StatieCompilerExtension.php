<?php declare(strict_types=1);

namespace Symplify\Statie\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\Statie\Console\ConsoleApplication;
use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Contract\Renderable\Routing\RouteCollectorInterface;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Source\SourceFileStorage;

final class StatieCompilerExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile(): void
    {
        $this->loadConsoleApplicationWithCommands();
        $this->loadSourceFileStorageWithSourceFileFilters();
        $this->loadRouterDecoratorWithRoutes();
    }

    private function loadConsoleApplicationWithCommands(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ConsoleApplication::class,
            Command::class,
            'add'
        );
    }

    private function loadSourceFileStorageWithSourceFileFilters(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            SourceFileStorage::class,
            SourceFileFilterInterface::class,
            'addSourceFileFilter'
        );
    }

    private function loadRouterDecoratorWithRoutes(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            RouteCollectorInterface::class,
            RouteInterface::class,
            'addRoute'
        );
    }
}
