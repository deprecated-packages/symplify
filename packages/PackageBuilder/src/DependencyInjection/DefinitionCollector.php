<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DefinitionCollector
{
    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    public function __construct(DefinitionFinder $definitionFinder)
    {
        $this->definitionFinder = $definitionFinder;
    }

    public function loadCollectorWithType(
        ContainerBuilder $containerBuilder,
        string $collectorType,
        string $collectedType,
        string $setterMethod
    ): void {
        $collectorDefinitions = $this->definitionFinder->findAllByType($containerBuilder, $collectorType);
        $collectedDefinitions = $this->definitionFinder->findAllByType($containerBuilder, $collectedType);

        foreach ($collectorDefinitions as $collectorDefinition) {
            foreach (array_keys($collectedDefinitions) as $name) {
                $collectorDefinition->addMethodCall($setterMethod, [new Reference($name)]);
            }
        }
    }
}
