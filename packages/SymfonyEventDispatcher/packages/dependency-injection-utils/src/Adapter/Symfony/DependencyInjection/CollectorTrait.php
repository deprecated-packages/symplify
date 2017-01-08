<?php declare(strict_types=1);

namespace Symplify\DependencyInjectionUtils\Adapter\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

trait CollectorTrait
{
    // @todo: consider making class with ContainerBuilder in ctor instead of trait
    use GetDefinitionByTypeTrait;

    public function loadCollectorWithType(
        ContainerBuilder $containerBuilder, string $collectorType, string $collectedType, string $setterMethod
    ) : void {
        $collectorDefinition = $this->getDefinitionByType($containerBuilder, $collectorType);

        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            if (! is_subclass_of($definition->getClass(), $collectedType)) {
                return;
            }

            $collectorDefinition->addMethodCall($setterMethod, [new Reference($name)]);
        }
    }
}
