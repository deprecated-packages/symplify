<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\SymfonyEventDispatcherBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_symfony_event_dispatcher' . mt_rand(1, 100), true);
    }

    public function registerBundles() : array
    {
        return [
            new SymfonyEventDispatcherBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
