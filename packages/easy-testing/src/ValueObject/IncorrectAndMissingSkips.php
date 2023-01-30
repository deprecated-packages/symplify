<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class IncorrectAndMissingSkips
{
    /**
     * @param SmartFileInfo[] $incorrectSkipFileInfos
     * @param SmartFileInfo[] $missingSkipFileInfos
     */
    public function __construct(
        private readonly array $incorrectSkipFileInfos,
        private readonly array $missingSkipFileInfos,
    ) {
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getIncorrectSkipFileInfos(): array
    {
        return $this->incorrectSkipFileInfos;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getMissingSkipFileInfos(): array
    {
        return $this->missingSkipFileInfos;
    }

    public function getFileCount(): int
    {
        return count($this->missingSkipFileInfos) + count($this->incorrectSkipFileInfos);
    }
}
