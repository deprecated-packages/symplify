<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\NetteAdapterForSymfonyBundles\Transformer\DI;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\ContainerBuilder;
use Symplify\NetteAdapterForSymfonyBundles\Transformer\ArgumentsTransformer;

final class TransformerFactory
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var string
     */
    private $tempDir;

    public function __construct(ContainerBuilder $containerBuilder, string $tempDir)
    {
        $this->containerBuilder = $containerBuilder;
        $this->tempDir = $tempDir;
    }

    public function create() : Container
    {
        $configurator = new Configurator();
        $configurator->addConfig(__DIR__ . '/services.neon');
        $configurator->setTempDirectory($this->tempDir);
        if (class_exists('Nette\Bridges\ApplicationDI\ApplicationExtension')) {
            $configurator->addConfig(__DIR__ . '/setup.neon');
        }
        $container = $configurator->createContainer();

        /** @var ArgumentsTransformer $argumentsTransformer */
        $argumentsTransformer = $container->getByType(ArgumentsTransformer::class);
        $argumentsTransformer->setContainerBuilder($this->containerBuilder);

        return $container;
    }
}
