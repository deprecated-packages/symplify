<?php

namespace Symplify\ActionAutowire\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ActionAutowire\SymplifyActionAutowireBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('ActionAutowire', true);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SymplifyActionAutowireBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
