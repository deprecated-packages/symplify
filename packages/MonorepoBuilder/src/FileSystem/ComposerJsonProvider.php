<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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

    public function __construct(JsonFileManager $jsonFileManager, PackageComposerFinder $packageComposerFinder)
    {
        $this->jsonFileManager = $jsonFileManager;
        $this->packageComposerFinder = $packageComposerFinder;
    }

    /**
     * @return mixed[]
     */
    public function getRootJson(): array
    {
        return $this->jsonFileManager->loadFromFilePath(getcwd() . '/composer.json');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getPackagesFileInfos(): array
    {
        return $this->packageComposerFinder->getPackageComposerFiles();
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getRootAndPackageFileInfos(): array
    {
        return array_merge($this->getPackagesFileInfos(), [$this->packageComposerFinder->getRootPackageComposerFile()]);
    }
}
