<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\NetteAdapterForSymfonyBundles\Transformer;

use Nette\DI\ContainerBuilder as NetteContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symplify\NetteAdapterForSymfonyBundles\Utils\Naming;

final class ContainerBuilderTransformer
{
    /**
     * @var ServiceDefinitionTransformer
     */
    private $serviceDefinitionTransformer;

    public function __construct(ServiceDefinitionTransformer $serviceDefinitionTransformer)
    {
        $this->serviceDefinitionTransformer = $serviceDefinitionTransformer;
    }

    public function transformFromNetteToSymfony(
        NetteContainerBuilder $netteContainerBuilder,
        SymfonyContainerBuilder $symfonyContainerBuilder
    ) {
        $netteServiceDefinitions = $netteContainerBuilder->getDefinitions();

        foreach ($netteServiceDefinitions as $name => $netteServiceDefinition) {
            $symfonyServiceDefinition = $this->serviceDefinitionTransformer->transformFromNetteToSymfony(
                $netteServiceDefinition
            );
            $symfonyContainerBuilder->setDefinition($name, $symfonyServiceDefinition);
        }
    }

    public function transformFromSymfonyToNette(
        SymfonyContainerBuilder $symfonyContainerBuilder,
        NetteContainerBuilder $netteContainerBuilder
    ) {
        $symfonyServiceDefinitions = $symfonyContainerBuilder->getDefinitions();

        foreach ($symfonyServiceDefinitions as $name => $symfonyServiceDefinition) {
            $name = Naming::sanitazeClassName((string) $name);
            if ($this->canServiceBeAdded($netteContainerBuilder, $name)) {
                $netteContainerBuilder->addDefinition(
                    $name,
                    $this->serviceDefinitionTransformer->transformFromSymfonyToNette($symfonyServiceDefinition)
                );
            }
        }

        $this->transformParametersFromSymfonyToNette($symfonyContainerBuilder, $netteContainerBuilder);
    }

    private function transformParametersFromSymfonyToNette(
        SymfonyContainerBuilder $symfonyContainerBuilder,
        NetteContainerBuilder $netteContainerBuilder
    ) {
        // transform parameters
        $parameterBag = $symfonyContainerBuilder->getParameterBag();
        foreach ($parameterBag->all() as $key => $value) {
            $netteContainerBuilder->parameters[$key] = $value;
        }
    }

    private function canServiceBeAdded(NetteContainerBuilder $netteContainerBuilder, string $name) : bool
    {
        $serviceNames = $this->getLowercasedNames($netteContainerBuilder);
        if (in_array($name, $serviceNames)) {
            return false;
        }

        return true;
    }

    /**
     * @return string[]
     */
    private function getLowercasedNames(NetteContainerBuilder $netteContainerBuilder) : array
    {
        $names = array_keys($netteContainerBuilder->getDefinitions());
        foreach ($names as $key => $name) {
            $names[$key] = strtolower((string) $name);
        }

        return $names;
    }
}
