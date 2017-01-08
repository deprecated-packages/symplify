<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('symplify_default_autowire' . mt_rand(1, 100), true);
    }

    public function registerBundles()
    {
        return [
            new SymplifyDefaultAutowireBundle(),
            new DoctrineBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}
