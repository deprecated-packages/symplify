<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassConstantsInFile
{
    /**
     * @var string[]
     */
    private array $nonExistingClassConstants = [];

    /**
     * @param string[] $nonExistingClassConstants
     */
    public function __construct(
        array $nonExistingClassConstants,
        private SmartFileInfo $fileInfo
    ) {
        $this->nonExistingClassConstants = $nonExistingClassConstants;
    }

    /**
     * @return string[]
     */
    public function getNonExistingClassConstants(): array
    {
        return $this->nonExistingClassConstants;
    }

    public function getFileInfo(): SmartFileInfo
    {
        return $this->fileInfo;
    }
}
