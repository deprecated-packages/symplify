<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class ConfigurableCollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @var bool
     */
    private $enableCommonCollectors = false;

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
        # symfony/event-dispatcher
        [
            'main_type' => 'Symfony\Component\EventDispatcher\EventDispatcherInterface',
            'collected_type' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
            'add_method' => 'addSubscriber',
        ],
        # symfony/console → symfony/event-dispatcher
        [
            'main_type' => 'Symfony\Component\Console\Application',
            'collected_type' => 'Symfony\Component\EventDispatcher\EventDispatcherInterface',
            'add_method' => 'setDispatcher',
        ],
    ];

    /**
     * @var DefinitionCollector
     */
    private $definitionCollector;

    public function __construct(bool $enableCommonCollectors = true)
    {
        sleep(3);

        $message = sprintf(
            '%s" is deprecated, because its magic causes to duplicated service adding.%sUse more explicit "%s" instead.',
            self::class,
            PHP_EOL,
            AutowireArrayParameterCompilerPass::class
        );
        trigger_error($message, E_USER_DEPRECATED);

        $this->definitionCollector = new DefinitionCollector(new DefinitionFinder());
        $this->enableCommonCollectors = $enableCommonCollectors;
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        if ($this->enableCommonCollectors) {
            foreach ($this->commonCollectors as $collector) {
                $this->loadCollector($containerBuilder, $collector);
            }
        }

        $parameterBag = $containerBuilder->getParameterBag();
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
