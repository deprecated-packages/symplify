<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\FlatWhite\Latte\LatteFactory;
use Symplify\Statie\Renderable\RenderableFilesProcessor;

final class CollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @var DefinitionCollector
     */
    private $definitionCollector;

    public function __construct()
    {
        $this->definitionCollector = new DefinitionCollector(new DefinitionFinder());
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->collectEventSubscribersEventDispatcher($containerBuilder);
        $this->collectCommandsToConsoleApplication($containerBuilder);
        $this->loadFilterProvidersToLatteFactory($containerBuilder);
        $this->loadFileDecoratorToRenderableFilesProcessor($containerBuilder);
    }

    private function collectCommandsToConsoleApplication(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            ConsoleApplication::class,
            Command::class,
            'add'
        );
    }

    private function loadFilterProvidersToLatteFactory(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            LatteFactory::class,
            FilterProviderInterface::class,
            'addFilterProvider'
        );
    }

    private function collectEventSubscribersEventDispatcher(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            EventDispatcherInterface::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }

    private function loadFileDecoratorToRenderableFilesProcessor(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            RenderableFilesProcessor::class,
            FileDecoratorInterface::class,
            'addFileDecorator'
        );
    }
}
