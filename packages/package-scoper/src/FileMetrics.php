<?php

declare(strict_types=1);

namespace Symplify\PackageScoper;

use Symplify\SmartFileSystem\SmartFileInfo;

final class FileMetrics
{
    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function getFileSizeInKiloBites(array $fileInfos): float
    {
        $fileSize = 0;
        foreach ($fileInfos as $fileInfo) {
            $fileSize += $fileInfo->getSize();
        }

        return $fileSize / 1024.0;
    }
}
