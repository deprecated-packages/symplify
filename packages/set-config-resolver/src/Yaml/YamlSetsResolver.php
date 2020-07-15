<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Yaml;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class YamlSetsResolver
{
    /**
     * @param string[] $configFiles
     * @return string[]
     */
    public function resolveFromConfigFiles(array $configFiles): array
    {
        $containerBuilder = new ContainerBuilder();

        $delegatingLoader = new DelegatingLoader(new LoaderResolver([
            new PhpFileLoader($containerBuilder, new FileLocator()),
            new YamlFileLoader($containerBuilder, new FileLocator()),
        ]));

        $sets = [];
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
            $sets += $containerBuilder->getParameter('sets');
        }

        return $sets;
    }
}
