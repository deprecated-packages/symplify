<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class SrcAndTestsDirectories
{
    /**
     * @var SmartFileInfo[]
     */
    private $srcDirectories = [];

    /**
     * @var SmartFileInfo[]
     */
    private $testsDirectories = [];

    /**
     * @param SmartFileInfo[] $srcDirectories
     * @param SmartFileInfo[] $testsDirectories
     */
    public function __construct(array $srcDirectories, array $testsDirectories)
    {
        $this->srcDirectories = $srcDirectories;
        $this->testsDirectories = $testsDirectories;
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
