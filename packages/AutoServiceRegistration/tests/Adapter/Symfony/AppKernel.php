<?php declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\Adapter\Symfony;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutoServiceRegistration\Adapter\Symfony\SymplifyAutoServiceRegistrationBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_auto_service_registration' . random_int(1, 100), true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new SymplifyAutoServiceRegistrationBundle,
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
