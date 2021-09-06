<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Contract\Application;

use Symplify\EasyCI\ValueObject\FileError;
use Symplify\SmartFileSystem\SmartFileInfo;

interface FileProcessorInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileError[]
     */
    public function analyzeFileInfos(array $fileInfos): array;
}
