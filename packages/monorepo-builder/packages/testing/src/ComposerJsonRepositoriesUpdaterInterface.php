<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing;

use Symplify\SmartFileSystem\SmartFileInfo;

interface ComposerJsonRepositoriesUpdaterInterface
{
    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $packageNames
     * @return mixed[]
     */
    public function decoratePackageComposerJson(array $packageComposerJson, array $packageNames, SmartFileInfo $rootComposerJsonFileInfo, ?bool $symlink): array;
}
