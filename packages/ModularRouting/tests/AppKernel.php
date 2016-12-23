<?php

declare(strict_types=1);

namespace Symplify\ModularRouting\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ModularRouting\SymplifyModularRoutingBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_modular_routing' . mt_rand(1, 100), true);
    }

    public function registerBundles() : array
    {
        return [
            new FrameworkBundle(),
            new CmfRoutingBundle(),
            new SymplifyModularRoutingBundle(),
            new TwigBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
