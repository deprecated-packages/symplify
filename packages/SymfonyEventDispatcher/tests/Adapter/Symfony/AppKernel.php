<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\SymfonyEventDispatcherBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_symfony_event_dispatcher' . random_int(1, 100), true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new SymfonyEventDispatcherBundle,
            new FrameworkBundle
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
