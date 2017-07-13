<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Contract\Renderable\Routing\RouteCollectorInterface;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\FlatWhite\Latte\LatteFactory;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Source\SourceFileStorage;

final class CollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->collectCommandsToConsoleApplication($containerBuilder);
        $this->loadSourceFileStorageWithSourceFileFilters($containerBuilder);
        $this->loadRouterDecoratorWithRoutes($containerBuilder);
        $this->loadFilterProvidersToLatteFactory($containerBuilder);
        $this->loadFileDecoratorToRenderableFilesProcessor($containerBuilder);
    }

    private function collectCommandsToConsoleApplication(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            ConsoleApplication::class,
            Command::class,
            'add'
        );
    }

    private function loadSourceFileStorageWithSourceFileFilters(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            SourceFileStorage::class,
            SourceFileFilterInterface::class,
            'addSourceFileFilter'
        );
    }

    private function loadRouterDecoratorWithRoutes(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            RouteCollectorInterface::class,
            RouteInterface::class,
            'addRoute'
        );
    }

    private function loadFilterProvidersToLatteFactory(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            LatteFactory::class,
            FilterProviderInterface::class,
            'addFilterProvider'
        );
    }

    private function loadFileDecoratorToRenderableFilesProcessor(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            RenderableFilesProcessor::class,
            FileDecoratorInterface::class,
            'addFileDecorator'
        );
    }
}
