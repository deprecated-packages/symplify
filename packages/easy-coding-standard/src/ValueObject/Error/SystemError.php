<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\SmartFileSystem\SmartFileInfo;

final class SystemError
{
    public function __construct(
        private int $line,
        private string $message,
        private SmartFileInfo $fileInfo
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFileWithLine(): string
    {
        return $this->fileInfo->getRelativeFilePathFromCwd() . ':' . $this->line;
    }
}
