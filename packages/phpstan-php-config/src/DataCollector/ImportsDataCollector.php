<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\DataCollector;

use Symplify\SmartFileSystem\SmartFileInfo;

final class ImportsDataCollector
{
    /**
     * @var string[]
     */
    private $filePaths = [];

    public function addImport(string $filePath): void
    {
        $this->filePaths[] = $filePath;
    }

    /**
     * @return string[]
     */
    public function getFilePaths(): array
    {
        $relativeFilePaths = [];
        foreach ($this->filePaths as $filePath) {
            $fileInfo = new SmartFileInfo($filePath);
            $relativeFilePaths[] = $fileInfo->getRelativeFilePathFromCwdInTests();
        }

        return $relativeFilePaths;
    }
}
