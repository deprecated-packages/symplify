<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\HttpKernel;

use Nette\Utils\FileSystem;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Application\Version\VersionResolver;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\Skipper\Bundle\SkipperBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyCodingStandardKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [
            new EasyCodingStandardBundle(),
            new SymplifyCodingStandardBundle(),
            new ConsoleColorDiffBundle(),
            new SymplifyKernelBundle(),
            new SkipperBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/ecs_' . get_current_user();
    }

    public function getLogDir(): string
    {
        $logDirectory = sys_get_temp_dir() . '/ecs_log_' . get_current_user();

        if (VersionResolver::PACKAGE_VERSION !== '@package_version@') {
            $logDirectory .= '_' . VersionResolver::PACKAGE_VERSION;
        }

        return $logDirectory;
    }

    public function boot(): void
    {
        $cacheDir = $this->getCacheDir();

        // Rebuild the container on each run
        FileSystem::delete($cacheDir);
        FileSystem::createDir($cacheDir);

        parent::boot();
    }

    protected function prepareContainer(ContainerBuilder $containerBuilder): void
    {
        // works better with workers - see https://github.com/symfony/symfony/pull/32581
        $containerBuilder->setParameter('container.dumper.inline_factories', true);
        parent::prepareContainer($containerBuilder);
    }

    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $delegatingLoaderFactory = new DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
