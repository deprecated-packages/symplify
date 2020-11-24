<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Yaml;

use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileInfo;

final class YamlImportsFactory
{
    public function createFromPhpFileInfo(SmartFileInfo $fileInfo): string
    {
        $yamlImportData = [
            'imports' => [
                [
                    'resource' => $fileInfo->getBasenameWithoutSuffix() . '.php',
                ],
            ],
        ];

        return Yaml::dump($yamlImportData) . PHP_EOL;
    }
}
