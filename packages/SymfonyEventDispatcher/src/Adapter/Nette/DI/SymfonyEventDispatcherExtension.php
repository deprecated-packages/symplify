<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionFinder;

final class SymfonyEventDispatcherExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        if ($this->isKdybyEventsRegistered()) {
            return;
        }

        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../../../config/services.neon')['services']
        );
    }

    public function beforeCompile(): void
    {
        $eventDispatcher = DefinitionFinder::getByType($this->getContainerBuilder(), EventDispatcherInterface::class);

        if ($this->isKdybyEventsRegistered()) {
            $eventDispatcher->setClass(EventDispatcher::class)
                ->setFactory(null);
        }

        $this->addSubscribersToEventDispatcher();
        $this->bindEventDispatcherToSymfonyConsole();
        $this->bindNetteEvents();
    }

    private function isKdybyEventsRegistered(): bool
    {
        return (bool) $this->compiler->getExtensions('Kdyby\Events\DI\EventsExtension');
    }

    private function addSubscribersToEventDispatcher(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $dispatcherDefinition = $containerBuilder->getDefinitionByType(EventDispatcherInterface::class);
        $subscriberDefinitions = $containerBuilder->findByType(EventSubscriberInterface::class);

        foreach ($subscriberDefinitions as $name => $definition) {
            $this->registerSubscriber($dispatcherDefinition, $name, $definition->getClass());
        }
    }

    private function registerSubscriber(ServiceDefinition $dispatcher, string $service, string $class): void
    {
        foreach ($class::getSubscribedEvents() as $event => $listeners) {
            if (is_string($listeners)) {
                $listeners = [[$listeners]];
            } elseif (is_string($listeners[0])) {
                $listeners = [$listeners];
            }
            foreach ($listeners as $listener) {
                $this->registerLazyListener($dispatcher, $event, $service, $listener[0], $listener[1] ?? 0);
            }
        }
    }

    private function registerLazyListener(
        ServiceDefinition $dispatcher,
        string $event,
        string $service,
        string $method,
        int $priority
    ): void {
        $dispatcher->addSetup(
            '?->addListener(?, function (...$arguments) { $this->getService(?)->?(...$arguments); }, ?)',
            [
                '@self',
                $event,
                $service,
                $method,
                $priority,
            ]
        );
    }

    private function bindNetteEvents(): void
    {
        $containerBuilder = $this->getContainerBuilder();

        $netteEventList = (new NetteEventListFactory)->create();
        foreach ($netteEventList as $netteEvent) {
            if (! $serviceDefinitions = $containerBuilder->findByType($netteEvent->getClass())) {
                return;
            }

            foreach ($serviceDefinitions as $serviceDefinition) {
                $this->decorateServiceDefinitionWithNetteEvent($serviceDefinition, $netteEvent);
            }
        }
    }

    private function decorateServiceDefinitionWithNetteEvent(
        ServiceDefinition $serviceDefinition,
        NetteEventItem $netteEvent
    ): void {
        $propertyStatement = new Statement('function () {
			$class = ?;
			$event = new $class(...func_get_args());
			?->dispatch(?, $event);
		}', [
            $netteEvent->getEventClass(),
            '@' . EventDispatcherInterface::class,
            $netteEvent->getEventName()
        ]);

        $serviceDefinition->addSetup('$service->?[] = ?;', [$netteEvent->getProperty(), $propertyStatement]);
    }

    private function bindEventDispatcherToSymfonyConsole(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        if ($consoleApplicationName = $containerBuilder->getByType('Symfony\Component\Console\Application')) {
            $containerBuilder->getDefinition($consoleApplicationName)
                ->addSetup('setDispatcher');
        }
    }
}
