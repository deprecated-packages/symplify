<?php declare(strict_types=1);

namespace Symplify\Statie\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\Statie\Console\ConsoleApplication;
use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Renderable\Routing\RouteDecorator;
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
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ConsoleApplication::class,
            Command::class,
            'add'
        );

        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            SourceFileStorage::class,
            SourceFileFilterInterface::class,
            'addSourceFileFilter'
        );

        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            RouteDecorator::class,
            RouteInterface::class,
            'addRoute'
        );
    }
}
