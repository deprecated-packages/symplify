<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class NonExistingClassConstantsInFile
{
    /**
     * @var string[]
     */
    private $nonExistingClassConstants = [];

    /**
     * @var SmartFileInfo
     */
    private $fileInfo;

    /**
     * @param string[] $nonExistingClassConstants
     */
    public function __construct(array $nonExistingClassConstants, SmartFileInfo $fileInfo)
    {
        $this->nonExistingClassConstants = $nonExistingClassConstants;
        $this->fileInfo = $fileInfo;
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
