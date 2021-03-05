<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassesInFile
{
    /**
     * @var string[]
     */
    private $nonExistingClasses = [];

    /**
     * @var SmartFileInfo
     */
    private $fileInfo;

    /**
     * @param string[] $nonExistingClasses
     */
    public function __construct(array $nonExistingClasses, SmartFileInfo $fileInfo)
    {
        $this->nonExistingClasses = $nonExistingClasses;
        $this->fileInfo = $fileInfo;
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
