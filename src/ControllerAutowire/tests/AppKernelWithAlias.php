<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;
use Symplify\ControllerAutowire\Tests\AliasingBundle\AliasingBundle;

final class AppKernelWithAlias extends Kernel
{
    public function registerBundles() : array
    {
        return [
            new FrameworkBundle(),
            new SymplifyControllerAutowireBundle(),
            new AliasingBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
