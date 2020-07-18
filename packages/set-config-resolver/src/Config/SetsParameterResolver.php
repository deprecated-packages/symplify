<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Config;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetsParameterResolver
{
    /**
     * @param string[] $configFiles
     * @return string[]
     */
    public function resolveFromConfigFiles(array $configFiles): array
    {
        $sets = [];
        foreach ($configFiles as $configFile) {
            $configFileInfo = new SmartFileInfo($configFile);
            $sets += $this->resolveSetsFromFileInfo($configFileInfo);
        }

        return $sets;
    }

    /**
     * @return string[]
     */
    private function resolveSetsFromFileInfo(SmartFileInfo $configFileInfo): array
    {
        if (in_array($configFileInfo->getSuffix(), ['yml', 'yaml'], true)) {
            $configContent = Yaml::parse($configFileInfo->getContents());
            return $configContent['parameters']['sets'] ?? [];
        }

        // php file loader
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());
        $phpFileLoader->load($configFileInfo->getRealPath());

        if (! $containerBuilder->hasParameter('sets')) {
            return [];
        }

        return $containerBuilder->getParameter('sets');
    }
}
