<?php

declare(strict_types = 1);

namespace Symplify\PHP7_CodeSniffer\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

/**
 * @method ContainerBuilder getContainerBuilder()
 */
trait ExtensionHelperTrait
{
    public function addServicesToCollector(
        string $collectorClass,
        string $collectedClass,
        string $adderMethodName
    ) {
        $containerBuilder = $this->getContainerBuilder();
        $collectorDefinition = $this->getDefinitionByType($collectorClass);

        foreach ($containerBuilder->findByType($collectedClass) as $name => $definition) {
            $collectorDefinition->addSetup(
                $adderMethodName,
                ['@'.$name]
            );
        }
    }

    public function getDefinitionByType(string $type) : ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();
        $definitionName = $containerBuilder->getByType($type);

        return $containerBuilder->getDefinition($definitionName);
    }
}
