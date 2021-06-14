<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackageNamesProvider
{
    /**
     * @var string[]
     */
    private array $names = [];

    public function __construct(
        private ComposerJsonProvider $composerJsonProvider,
        private JsonFileManager $jsonFileManager
    ) {
    }

    /**
     * @return string[]
     */
    public function provide(): array
    {
        if ($this->names !== []) {
            return $this->names;
        }

        $packagesFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();
        foreach ($packagesFileInfos as $packagesFileInfo) {
            $name = $this->extractNameFromFileInfo($packagesFileInfo);
            if ($name !== null) {
                $this->names[] = $name;
            }
        }

        return $this->names;
    }

    private function extractNameFromFileInfo(SmartFileInfo $smartFileInfo): ?string
    {
        $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);

        return $json['name'] ?? null;
    }
}
