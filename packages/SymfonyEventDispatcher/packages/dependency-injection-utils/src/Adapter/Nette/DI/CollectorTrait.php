<?php declare(strict_types=1);

namespace Symplify\DependencyInjectionUtils\Adapter\Nette\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

/**
 * @method ContainerBuilder getContainerBuilder()
 */
trait CollectorTrait
{
    use GetDefinitionByTypeTrait;

    public function loadCollectorWithType(string $collectorType, string $collectedType, string $setterMethod) : void
    {
        $collectorDefinition = $this->getDefinitionByType($collectorType);

        $collectedDefinitions = $this->getContainerBuilder()
            ->findByType($collectedType);

        foreach ($collectedDefinitions as $name => $definition) {
            $collectorDefinition->addSetup($setterMethod, ['@' . $name]);
        }
    }
}
