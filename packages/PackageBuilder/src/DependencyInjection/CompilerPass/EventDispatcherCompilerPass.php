<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class EventDispatcherCompilerPass implements CompilerPassInterface
{
    /**
     * @var DefinitionCollector
     */
    private $definitionCollector;

    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    public function __construct()
    {
        $this->definitionFinder = new DefinitionFinder();
        $this->definitionCollector = new DefinitionCollector($this->definitionFinder);
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        // register event dispatcher if missing
        if (! $this->definitionFinder->getByTypeIfExists($containerBuilder, EventDispatcherInterface::class)) {
            $containerBuilder->autowire(EventDispatcher::class, EventDispatcher::class);
        }

        $this->collectEventSubscribersToEventDispatcher($containerBuilder);
        $this->addEventDispatcherToConsole($containerBuilder);
    }

    private function collectEventSubscribersToEventDispatcher(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            EventDispatcherInterface::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }

    private function addEventDispatcherToConsole(ContainerBuilder $containerBuilder): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            Application::class,
            EventDispatcherInterface::class,
            'setDispatcher'
        );
    }
}
