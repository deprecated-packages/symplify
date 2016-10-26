<?php

namespace Symplify\AutoServiceRegistration\Tests\Symfony;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutoServiceRegistration\Symfony\SymplifyAutoServiceRegistrationBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct(rand(1, 100), true);
    }

    public function registerBundles() : array
    {
        return [
            new SymplifyAutoServiceRegistrationBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
