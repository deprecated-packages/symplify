<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Config;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Yaml\Yaml;
use Symplify\SetConfigResolver\SetResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetsParameterResolver
{
    /**
     * @var string
     */
    private const SETS = 'sets';

    /**
     * @var SetResolver
     */
    private $setResolver;

    public function __construct(SetResolver $setResolver)
    {
        $this->setResolver = $setResolver;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return SmartFileInfo[]
     */
    public function resolveFromFileInfos(array $fileInfos): array
    {
        $setFileInfos = [];
        foreach ($fileInfos as $fileInfo) {
            $setsNames = $this->resolveSetsFromFileInfo($fileInfo);
            foreach ($setsNames as $setsName) {
                $setFileInfos[] = $this->setResolver->detectFromName($setsName);
            }
        }

        return $setFileInfos;
    }

    /**
     * @return string[]
     */
    private function resolveSetsFromFileInfo(SmartFileInfo $configFileInfo): array
    {
        if ($configFileInfo->hasSuffixes(['yml', 'yaml'])) {
            return $this->resolveSetsParameterFromYamlFileInfo($configFileInfo);
        }

        return $this->resolveSetsParameterFromPhpFileInfo($configFileInfo);
    }

    /**
     * @return string[]
     */
    private function resolveSetsParameterFromYamlFileInfo(SmartFileInfo $configFileInfo): array
    {
        $configContent = Yaml::parse($configFileInfo->getContents());

        return (array) ($configContent['parameters'][self::SETS] ?? []);
    }

    /**
     * @return string[]
     */
    private function resolveSetsParameterFromPhpFileInfo(SmartFileInfo $configFileInfo): array
    {
        // php file loader
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());
        $phpFileLoader->load($configFileInfo->getRealPath());

        if (! $containerBuilder->hasParameter(self::SETS)) {
            return [];
        }

        return (array) $containerBuilder->getParameter(self::SETS);
    }
}
