<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing;

use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerJsonSymlinker;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonRepositoriesUpdater
{
    /**
     * @var ComposerJsonSymlinker
     */
    private $composerJsonSymlinker;

    public function __construct(ComposerJsonSymlinker $composerJsonSymlinker) {
        $this->composerJsonSymlinker = $composerJsonSymlinker;
    }

    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $packageNames
     * @return mixed[]
     */
    protected function decoratePackageComposerJson(array $packageComposerJson, array $packageNames, SmartFileInfo $rootComposerJsonFileInfo, ?bool $symlink): array
    {
        return $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            $packageNames,
            $rootComposerJsonFileInfo,
            $symlink
        );
    }
}
