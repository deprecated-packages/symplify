<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Nette\DI;

use Nette\DI\ContainerBuilder;

final class DefinitionCollector
{
    public static function loadCollectorWithType(
        ContainerBuilder $containerBuilder,
        string $collectorType,
        string $collectedType,
        string $setterMethod
    ) : void {
        $collectorDefinition = $containerBuilder->getDefinitionByType($collectorType);
        $collectedDefinitions = $containerBuilder->findByType($collectedType);
        foreach ($collectedDefinitions as $name => $definition) {
            $collectorDefinition->addSetup($setterMethod, ['@' . $name]);
        }
    }
}
