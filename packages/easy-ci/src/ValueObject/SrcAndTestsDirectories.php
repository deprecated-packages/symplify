<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class SrcAndTestsDirectories
{
    /**
     * @param SmartFileInfo[] $srcDirectories
     * @param SmartFileInfo[] $testsDirectories
     */
    public function __construct(
        private array $srcDirectories,
        private array $testsDirectories
    ) {
    }

    /**
     * @return string[]
     */
    public function getRelativePathSrcDirectories(): array
    {
        $relativePaths = [];
        foreach ($this->srcDirectories as $srcDirectoryFileInfo) {
            $relativePaths[] = $srcDirectoryFileInfo->getRelativeFilePathFromCwd();
        }

        sort($relativePaths);

        return $relativePaths;
    }

    /**
     * @return string[]
     */
    public function getRelativePathTestsDirectories(): array
    {
        $relativePaths = [];
        foreach ($this->testsDirectories as $testsDirectoryFileInfo) {
            $relativePaths[] = $testsDirectoryFileInfo->getRelativeFilePathFromCwd();
        }

        sort($relativePaths);

        return $relativePaths;
    }
}
