<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\NetteAdapterForSymfonyBundles\Transformer;

use Nette\DI\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

final class ParametersTransformer
{
    /**
     * @var SymfonyContainerBuilder
     */
    private $symfonyContainerBuilder;

    public function __construct(SymfonyContainerBuilder $symfonyContainerBuilder)
    {
        $this->symfonyContainerBuilder = $symfonyContainerBuilder;
    }

    public function transformFromNetteToSymfony(Compiler $compiler, array $extensionConfig)
    {
        $this->symfonyContainerBuilder->setParameter('kernel.bundles', $extensionConfig['bundles']);

        $netteConfig = $compiler->getConfig()['parameters'];
        $this->symfonyContainerBuilder->setParameter('kernel.root_dir', $netteConfig['appDir']);
        $this->symfonyContainerBuilder->setParameter('kernel.cache_dir', $netteConfig['tempDir']);
        $this->symfonyContainerBuilder->setParameter('kernel.logs_dir', $netteConfig['tempDir']);
        $this->symfonyContainerBuilder->setParameter('kernel.debug', $netteConfig['debugMode']);
        $this->symfonyContainerBuilder->setParameter(
            'kernel.environment',
            $netteConfig['productionMode'] ? 'prod' : 'dev'
        );
    }
}
