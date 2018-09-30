<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class ConfigurableCollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @var DefinitionCollector
     */
    private $definitionCollector;

    /**
     * @var string[][]
     */
    private $commonCollectors = [
        # symfony/console
        [
            'main_type' => 'Symfony\Component\Console\Application',
            'collected_type' => 'Symfony\Component\Console\Command\Command',
            'add_method' => 'add',
        ],
        # symfony/event-subscriber
        [
            'main_type' => 'Symfony\Component\EventDispatcher\EventDispatcherInterface',
            'collected_type' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
            'add_method' => 'addSubscriber',
        ],
    ];

    /**
     * @var bool
     */
    private $enableCommonCollectors = false;

    public function __construct(bool $enableCommonCollectors = true)
    {
        $this->definitionCollector = new DefinitionCollector(new DefinitionFinder());
        $this->enableCommonCollectors = $enableCommonCollectors;
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $parameterBag = $containerBuilder->getParameterBag();

        foreach ($this->defaultCollectors as $collector) {
            $this->loadCollector($containerBuilder, $collector);
        }

        if (! $parameterBag->has('collectors')) {
            return;
        }

        foreach ((array) $parameterBag->get('collectors') as $collector) {
            $this->loadCollector($containerBuilder, $collector);
        }
    }

    /**
     * @param mixed[] $collector
     */
    private function loadCollector(ContainerBuilder $containerBuilder, array $collector): void
    {
        $this->definitionCollector->loadCollectorWithType(
            $containerBuilder,
            $collector['main_type'],
            $collector['collected_type'],
            $collector['add_method']
        );
    }
}
