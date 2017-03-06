<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_default_autowire' . random_int(1, 100), true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new SymplifyDefaultAutowireBundle,
            new DoctrineBundle,
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
