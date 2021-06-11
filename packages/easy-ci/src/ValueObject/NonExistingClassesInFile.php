<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassesInFile
{
    /**
     * @var string[]
     */
    private array $nonExistingClasses = [];

    /**
     * @param string[] $nonExistingClasses
     */
    public function __construct(
        array $nonExistingClasses,
        private SmartFileInfo $fileInfo
    ) {
        $this->nonExistingClasses = $nonExistingClasses;
    }

    /**
     * @return string[]
     */
    public function getNonExistingClasses(): array
    {
        return $this->nonExistingClasses;
    }

    public function getFilePath(): string
    {
        return $this->fileInfo->getRelativeFilePathFromCwd();
    }
}
