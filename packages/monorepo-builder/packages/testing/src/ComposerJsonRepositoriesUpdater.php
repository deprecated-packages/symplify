<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerJsonSymlinker;
use Symplify\MonorepoBuilder\Testing\PackageDependency\UsedPackagesResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonRepositoriesUpdater extends AbstractComposerJsonRepositoriesUpdater
{
    /**
     * @var ComposerJsonSymlinker
     */
    private $composerJsonSymlinker;

    public function __construct(
        PackageNamesProvider $packageNamesProvider,
        JsonFileManager $jsonFileManager,
        SymfonyStyle $symfonyStyle,
        UsedPackagesResolver $usedPackagesResolver,
        ConsoleDiffer $consoleDiffer,
        ComposerJsonSymlinker $composerJsonSymlinker
    ) {
        $this->composerJsonSymlinker = $composerJsonSymlinker;

        parent::__construct(
            $packageNamesProvider,
            $jsonFileManager,
            $symfonyStyle,
            $usedPackagesResolver,
            $consoleDiffer
        );
    }

    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $packageNames
     * @return mixed[]
     */
    public function decoratePackageComposerJson(array $packageComposerJson, array $packageNames, SmartFileInfo $rootComposerJsonFileInfo, ?bool $symlink): array
    {
        return $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            $packageNames,
            $rootComposerJsonFileInfo,
            $symlink
        );
    }
}
