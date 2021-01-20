<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\EasyCI\Exception\ShouldNotHappenException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class UpdateReplaceReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(ComposerJsonProvider $composerJsonProvider, JsonFileManager $jsonFileManager)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
    }

    public function work(Version $version): void
    {
        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        $replace = $rootComposerJson->getReplace();

        $newReplace = [];
        foreach (array_keys($replace) as $package) {
            $newReplace[$package] = $version->getVersionString();
        }

        if ($replace === $newReplace) {
            return;
        }

        $rootComposerJson->setReplace($newReplace);

        $rootFileInfo = $rootComposerJson->getFileInfo();
        if (! $rootFileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }

        $this->jsonFileManager->printJsonToFileInfo($rootComposerJson->getJsonArray(), $rootFileInfo);
    }

    public function getDescription(Version $version): string
    {
        return 'Update "replace" version in "composer.json" to new tag to avoid circular dependencies conflicts';
    }
}
