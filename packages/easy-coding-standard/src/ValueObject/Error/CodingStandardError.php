<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\SmartFileSystem\SmartFileInfo;

final class CodingStandardError implements \JsonSerializable
{
    public function __construct(
        private int $line,
        private string $message,
        private string $checkerClass,
        private SmartFileInfo $fileInfo
    ) {
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCheckerClass(): string
    {
        return $this->checkerClass;
    }

    public function getFileWithLine(): string
    {
        return $this->getRelativeFilePathFromCwd() . ':' . $this->line;
    }

    public function getRelativeFilePathFromCwd(): string
    {
        return $this->fileInfo->getRelativeFilePathFromCwd();
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'line' => $this->line,
            'message' => $this->message,
            'checker_class' => $this->checkerClass,
            'file_with_line' => $this->getFileWithLine(),
            'relative_file_path_from_cwd' => $this->getRelativeFilePathFromCwd(),
        ];
    }
}
