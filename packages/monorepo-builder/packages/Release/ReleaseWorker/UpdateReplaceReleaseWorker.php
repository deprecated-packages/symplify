<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Exception\MissingComposerJsonException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class UpdateReplaceReleaseWorker implements ReleaseWorkerInterface
{
    public function __construct(
        private ComposerJsonProvider $composerJsonProvider,
        private JsonFileManager $jsonFileManager
    ) {
    }

    public function work(Version $version): void
    {
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        $replace = $rootComposerJson->getReplace();

        $packageNames = $this->composerJsonProvider->getPackageNames();

        $newReplace = [];
        foreach (array_keys($replace) as $package) {
            if (! in_array($package, $packageNames, true)) {
                continue;
            }

            $newReplace[$package] = $version->getVersionString();
        }

        if ($replace === $newReplace) {
            return;
        }

        $rootComposerJson->setReplace($newReplace);

        $rootFileInfo = $rootComposerJson->getFileInfo();
        if (! $rootFileInfo instanceof SmartFileInfo) {
            throw new MissingComposerJsonException();
        }

        $this->jsonFileManager->printJsonToFileInfo($rootComposerJson->getJsonArray(), $rootFileInfo);
    }

    public function getDescription(Version $version): string
    {
        return 'Update "replace" version in "composer.json" to new tag to avoid circular dependencies conflicts';
    }
}
