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
     * @var string
     */
    private const EXTRA = 'extra';

    /**
     * @var string
     */
    private const BRANCH_ALIAS = 'branch-alias';

    /**
     * @var string
     */
    private const DEV_MASTER = 'dev-master';

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

            $json[self::EXTRA][self::BRANCH_ALIAS][self::DEV_MASTER] = $alias;

            $this->jsonFileManager->printJsonToFileInfo($json, $fileInfo);
        }
    }

    /**
     * @param mixed[] $json
     */
    private function shouldSkip(array $json, string $alias): bool
    {
        // update only when already present
        if (! isset($json[self::EXTRA][self::BRANCH_ALIAS][self::DEV_MASTER])) {
            return true;
        }

        $currentAlias = $json[self::EXTRA][self::BRANCH_ALIAS][self::DEV_MASTER];

        return $currentAlias === $alias;
    }
}
