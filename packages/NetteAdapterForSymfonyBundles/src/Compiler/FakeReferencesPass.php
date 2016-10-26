<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\NetteAdapterForSymfonyBundles\Compiler;

use ReflectionClass;
use stdClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will disable all missing references to named services.
 * Missing parameter resolution shall be resolved in Nette's ContainerBuilder.
 *
 * Based on @see \Symfony\Component\DependencyInjection\Compiler\CheckExceptionOnInvalidReferenceBehaviorPass
 */
final class FakeReferencesPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        foreach ($container->getDefinitions() as $id => $definition) {
            $this->processDefinition($definition);
        }
    }

    /**
     * @param mixed $definition
     */
    private function processDefinition($definition)
    {
        if (! $definition instanceof Definition) {
            return;
        }

        $this->processReferences($definition->getArguments());
        $this->processReferences($definition->getMethodCalls());
        $this->processReferences($definition->getProperties());
    }

    /**
     * @param mixed $arguments
     */
    private function processReferences($arguments)
    {
        if (! is_array($arguments)) {
            return;
        }

        foreach ($arguments as $argument) {
            $this->processReferences($argument);
            $this->processDefinition($argument);
            $this->addMissingDefinitionReferenceToContainer($argument);
        }
    }

    /**
     * @param mixed $argument
     */
    private function addMissingDefinitionReferenceToContainer($argument)
    {
        if (! $this->isMissingDefinitionReference($argument)) {
            return;
        }

        $serviceName = (string) $argument;
        if (class_exists($serviceName)) {
            $serviceName = (new ReflectionClass($serviceName))->name;
        }

        if (! $this->container->has($serviceName)) {
            $this->container->setDefinition($serviceName, new Definition(stdClass::class));
        }
    }

    /**
     * @param mixed $argument
     */
    private function isMissingDefinitionReference($argument) : bool
    {
        return $argument instanceof Reference
        && $argument->getInvalidBehavior() === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
    }
}
