<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Adapter\Symfony;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ModularDoctrineFilters\Adapter\Symfony\ModularDoctrineFiltersBundle;

final class AppKernel extends Kernel
{
    public function registerBundles() : array
    {
        return [
            new FrameworkBundle,
            new DoctrineBundle,
            new ModularDoctrineFiltersBundle,
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }

    public function getCacheDir() : string
    {
        return sys_get_temp_dir() . '/modular-doctrine-filters';
    }

    public function getLogDir() : string
    {
        return sys_get_temp_dir() . '/modular-doctrine-filters';
    }
}
