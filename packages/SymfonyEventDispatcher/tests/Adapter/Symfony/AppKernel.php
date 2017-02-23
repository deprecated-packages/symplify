<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\SymfonyEventDispatcherBundle;

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new SymfonyEventDispatcherBundle,
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/symplify_symfony_event_dispatcher';
    }

    public function getLogDir(): string
    {
           return sys_get_temp_dir() . '/symplify_symfony_event_dispatcher';
    }
}
