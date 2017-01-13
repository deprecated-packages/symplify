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
    ) : void {
        $collectorDefinition = DefinitionFinder::getByType($containerBuilder, $collectorType);
        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            if (! is_subclass_of($definition->getClass(), $collectedType)) {
                return;
            }

            $collectorDefinition->addMethodCall($setterMethod, [new Reference($name)]);
        }
    }
}
