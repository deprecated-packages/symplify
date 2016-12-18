<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularDoctrineFilters;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ModularDoctrineFilters\DependencyInjection\Compiler\LoadFiltersCompilerPass;
use Symplify\ModularDoctrineFilters\DependencyInjection\Extension\SymplifyModularDoctrineFiltersExtension;

final class SymplifyModularDoctrineFiltersBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension() : SymplifyModularDoctrineFiltersExtension
    {
        return new SymplifyModularDoctrineFiltersExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new LoadFiltersCompilerPass());
    }
}
