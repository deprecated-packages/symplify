<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileError implements FileErrorInterface
{
    public function __construct(
        private string $errorMessage,
        private SmartFileInfo $smartFileInfo
    ) {
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getRelativeFilePath(): string
    {
        return $this->smartFileInfo->getRelativeFilePath();
    }
}
