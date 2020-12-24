<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Finder\PackageComposerFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ComposerJsonProvider
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var PackageComposerFinder
     */
    private $packageComposerFinder;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var SmartFileInfo[]
     */
    private $packageComposerFiles;

    /**
     * @var array<string, mixed[]>
     */
    private $packageComposerFileJSONs;

    public function __construct(
        JsonFileManager $jsonFileManager,
        PackageComposerFinder $packageComposerFinder,
        ComposerJsonFactory $composerJsonFactory
    ) {
        $this->jsonFileManager = $jsonFileManager;
        $this->packageComposerFinder = $packageComposerFinder;
        $this->composerJsonFactory = $composerJsonFactory;
        $this->packageComposerFiles = [];
        $this->packageComposerFileJSONs = [];
    }

    public function getRootFileInfo(): SmartFileInfo
    {
        return $this->packageComposerFinder->getRootPackageComposerFile();
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getPackagesComposerFileInfos(): array
    {
        return $this->packageComposerFinder->getPackageComposerFiles();
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getRootAndPackageFileInfos(): array
    {
        return array_merge(
            $this->getPackagesComposerFileInfos(),
            [$this->packageComposerFinder->getRootPackageComposerFile()]
        );
    }

    public function getPackageFileInfoByName(string $packageName): SmartFileInfo
    {
        if ($this->packageComposerFiles === []) {
            $this->packageComposerFiles = $this->packageComposerFinder->getPackageComposerFiles();
        }
        foreach ($this->packageComposerFiles as $packageComposerFile) {
            $realPath = $packageComposerFile->getRealPath();
            if (! isset($this->packageComposerFileJSONs[$realPath])) {
                $this->packageComposerFileJSONs[$realPath] = $this->jsonFileManager->loadFromFileInfo($packageComposerFile);
            }
            $json = $this->packageComposerFileJSONs[$realPath];
            if (! isset($json['name'])) {
                continue;
            }

            if ($json['name'] !== $packageName) {
                continue;
            }

            return $packageComposerFile;
        }

        throw new ShouldNotHappenException();
    }

    public function getRootComposerJson(): ComposerJson
    {
        return $this->composerJsonFactory->createFromFileInfo($this->getRootFileInfo());
    }
}
