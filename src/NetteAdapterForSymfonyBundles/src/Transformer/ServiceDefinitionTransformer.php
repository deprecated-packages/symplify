<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\NetteAdapterForSymfonyBundles\Transformer;

use Nette\DI\ServiceDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ServiceDefinitionTransformer
{
    /**
     * @var ArgumentsTransformer
     */
    private $argumentsTransformer;

    public function __construct(ArgumentsTransformer $argumentsTransformer)
    {
        $this->argumentsTransformer = $argumentsTransformer;
    }

    public function transformFromSymfonyToNette(Definition $symfonyDefinition)
    {
        $netteDefinition = (new ServiceDefinition())
            ->setClass($symfonyDefinition->getClass())
            ->setTags($symfonyDefinition->getTags());

        foreach ($symfonyDefinition->getMethodCalls() as $methodCall) {
            $methodCallArguments = $this->argumentsTransformer->transformFromSymfonyToNette($methodCall[1]);
            $netteDefinition->addSetup($methodCall[0], $methodCallArguments);
        }

        $netteDefinition = $this->transformFactoryFromSymfonyToNette($symfonyDefinition, $netteDefinition);

        return $netteDefinition;
    }

    public function transformFromNetteToSymfony(ServiceDefinition $netteDefinition)
    {
        $tags = $this->transformTagsFromNetteToSymfony($netteDefinition->getTags());
        $symfonyDefinition = (new Definition())
            ->setClass($netteDefinition->getClass())
            ->setTags($tags);

        $symfonyDefinition = $this->transformFactoryFromNetteToSymfony($netteDefinition, $symfonyDefinition);

        return $symfonyDefinition;
    }

    private function transformTagsFromNetteToSymfony(array $tags) : array
    {
        foreach ($tags as $key => $tag) {
            if (!is_array($tag)) {
                $tags[$key] = [[$tag]];
            }
        }

        return $tags;
    }

    private function transformFactoryFromSymfonyToNette(
        Definition $symfonyDefinition,
        ServiceDefinition $netteDefinition
    ) : ServiceDefinition {
        if ($factory = $symfonyDefinition->getFactory()) {
            if (is_array($factory) && $factory[0] instanceof Reference) {
                $serviceReference = $factory[0];
                $createMethod = $factory[1];

                // note: possible issue - static vs dynamic?
                $factory = ['@'.$serviceReference, $createMethod];
            }
            $netteDefinition->setFactory($factory);
        }

        $netteDefinition->setArguments(
            $this->argumentsTransformer->transformFromSymfonyToNette($symfonyDefinition->getArguments())
        );

        return $netteDefinition;
    }

    private function transformFactoryFromNetteToSymfony(
        ServiceDefinition $netteDefinition,
        Definition $symfonyDefinition
    ) : Definition {
        if ($netteDefinition->getFactory()) {
            $factory = $netteDefinition->getFactory();
            $symfonyDefinition->setFactory($factory->getEntity());
            $symfonyDefinition->setArguments($factory->arguments);
        }

        return $symfonyDefinition;
    }
}
