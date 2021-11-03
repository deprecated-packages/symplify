<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Contract\Config;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory): LoaderInterface;
}
