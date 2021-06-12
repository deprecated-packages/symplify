<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class OldAndNewFileInfo
{
    public function __construct(
        private SmartFileInfo $oldFileInfo,
        private SmartFileInfo $newFileInfo,
        private string $packageName
    ) {
    }

    public function getOldFileInfo(): SmartFileInfo
    {
        return $this->oldFileInfo;
    }

    public function getOldFileRelativePath(): string
    {
        return $this->oldFileInfo->getRelativeFilePathFromCwd();
    }

    public function getNewFileRelativePath(): string
    {
        return $this->newFileInfo->getRelativeFilePathFromCwd();
    }

    public function getNewFileInfo(): SmartFileInfo
    {
        return $this->newFileInfo;
    }

    public function isContentIdentical(): bool
    {
        return $this->newFileInfo->getContents() === $this->oldFileInfo->getContents();
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }
}
