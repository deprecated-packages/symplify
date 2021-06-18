<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\SmartFileSystem\SmartFileInfo;

final class SystemError implements \JsonSerializable
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->message,
            'file_with_line' => $this->getFileWithLine(),
        ];
    }
}
