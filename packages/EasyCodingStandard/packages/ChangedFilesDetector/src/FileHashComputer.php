<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

final class FileHashComputer
{
    public function compute(string $filePath): string
    {
        if (! Strings::endsWith($filePath, '.yml') && ! Strings::endsWith($filePath, '.yaml')) {
            return md5_file($filePath);
        }

        $containerBuilder = new ContainerBuilder();
        $loader = $this->createLoaderForContainerBuilder($containerBuilder, new FileLocator(dirname($filePath)));
        $loader->load($filePath);

        return md5(serialize($containerBuilder));
    }

    private function createLoaderForContainerBuilder(
        ContainerBuilder $containerBuilder,
        FileLocatorInterface $fileLocator
    ): LoaderInterface {
        return new DelegatingLoader(
            new LoaderResolver([
                    new GlobFileLoader($containerBuilder, $fileLocator),
                    new CheckerTolerantYamlFileLoader($containerBuilder, $fileLocator),
                    new PhpFileLoader($containerBuilder, $fileLocator),
                ]
            ));
    }
}
