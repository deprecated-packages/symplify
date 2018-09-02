<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\PackageComposerFinder;

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
    public function getRootComposerJson(): array
    {
        return $this->jsonFileManager->loadFromFilePath(getcwd() . '/composer.json');
    }

    /**
     * @return SplFileInfo[]
     */
    public function getPackagesComposerJsonFileInfos(): array
    {
        return $this->packageComposerFinder->getPackageComposerFiles();
    }
}
