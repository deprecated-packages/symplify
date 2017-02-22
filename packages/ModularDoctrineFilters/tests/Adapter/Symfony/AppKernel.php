<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Adapter\Symfony;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nette\Utils\FileSystem;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ModularDoctrineFilters\Adapter\Symfony\ModularDoctrineFiltersBundle;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\SymfonyEventDispatcherBundle;

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle,
            new DoctrineBundle,
            new SymfonyEventDispatcherBundle,
            new ModularDoctrineFiltersBundle,
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }

    public function getCacheDir(): string
    {
        $cacheDir = sys_get_temp_dir() . '/modular-doctrine-filters';
        FileSystem::delete($cacheDir);
        FileSystem::createDir($cacheDir);
        return $cacheDir;
    }

    public function getLogDir(): string
    {
        return $this->getCacheDir();
    }
}
