<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\MonorepoBuilder\Tests\DevMasterAliasUpdater\DevMasterAliasUpdaterTest
 */
final class DevMasterAliasUpdater
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function updateFileInfosWithAlias(array $fileInfos, string $alias): void
    {
        foreach ($fileInfos as $fileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($fileInfo);
            if ($this->shouldSkip($json, $alias)) {
                continue;
            }

            $json['extra']['branch-alias']['dev-master'] = $alias;

            $this->jsonFileManager->saveJsonWithFileInfo($json, $fileInfo);
        }
    }

    /**
     * @param mixed[] $json
     */
    private function shouldSkip(array $json, string $alias): bool
    {
        // update only when already present
        if (! isset($json['extra']['branch-alias']['dev-master'])) {
            return true;
        }

        $currentAlias = $json['extra']['branch-alias']['dev-master'];

        return $currentAlias === $alias;
    }
}
