<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\DI\Helper;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

/**
 * @method ContainerBuilder getContainerBuilder()
 */
trait TypeAndCollectorTrait
{
    public function collectByType(string $collectorType, string $collectedType, string $setterMethod)
    {
        $collectorDefinition = $this->getDefinitionByType($collectorType);

        $collectedDefinitions = $this->getContainerBuilder()
            ->findByType($collectedType);
        foreach ($collectedDefinitions as $name => $definition) {
            $collectorDefinition->addSetup($setterMethod, ['@' . $name]);
        }
    }

    public function getDefinitionByType(string $type) : ServiceDefinition
    {
        $definitionName = $this->getContainerBuilder()
            ->getByType($type);

        return $this->getContainerBuilder()
            ->getDefinition($definitionName);
    }
}
