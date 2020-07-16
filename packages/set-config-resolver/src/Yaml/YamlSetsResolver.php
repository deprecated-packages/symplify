<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Yaml;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class YamlSetsResolver
{
    /**
     * @param string[] $configFiles
     * @return string[]
     */
    public function resolveFromConfigFiles(array $configFiles): array
    {
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());

        $sets = [];
        foreach ($configFiles as $configFile) {
            $configFileInfo = new SmartFileInfo($configFile);
            if (in_array($configFileInfo->getSuffix(), ['yml', 'yaml'], true)) {
                $configContent = Yaml::parse($configFileInfo->getContents());
                $sets += $configContent['parameters'][Option::SETS] ?? [];
            } else {
                $phpFileLoader->load($configFile);
                $sets += $containerBuilder->getParameter(Option::SETS);
            }
        }

        return $sets;
    }
}
