<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class DefinitionCollector
{
    public static function loadCollectorWithType(
        ContainerBuilder $containerBuilder,
        string $collectorType,
        string $collectedType,
        string $setterMethod
    ): void {
        $collectorDefinitions = DefinitionFinder::findAllByType($containerBuilder, $collectorType);
        $collectedDefinitions = DefinitionFinder::findAllByType($containerBuilder, $collectedType);

        foreach ($collectorDefinitions as $collectorDefinition) {
            foreach ($collectedDefinitions as $name => $collectedDefinition) {
                $collectorDefinition->addMethodCall($setterMethod, [new Reference($name)]);
            }
        }
    }
}
