<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;

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
     * @param SplFileInfo[] $fileInfos
     */
    public function updateFileInfosWithAlias(array $fileInfos, string $alias): void
    {
        foreach ($composerPackageFiles as $composerPackageFile) {
            $composerJson = $this->jsonFileManager->loadFromFileInfo($composerPackageFile);

            // update only when already present
            if (! isset($composerJson['extra']['branch-alias']['dev-master'])) {
                continue;
            }

            $currentAlias = $composerJson['extra']['branch-alias']['dev-master'];
            if ($currentAlias === $alias) {
                continue;
            }

            $composerJson['extra']['branch-alias']['dev-master'] = $alias;

            $this->jsonFileManager->saveJsonWithFileInfo($composerJson, $composerPackageFile);
        }
    }
}
