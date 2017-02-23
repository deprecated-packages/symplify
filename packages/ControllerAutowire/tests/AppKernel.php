<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_controller_autowire', true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle,
            new SymplifyControllerAutowireBundle,
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
