<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\ValueObject;

final class FilePathWithContent
{
    /**
     * @var array<int, string>
     */
    private $contentLines = [];

    public function __construct(
        private string $filePath,
        string $content
    ) {
        $this->contentLines = explode(PHP_EOL, $content);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string[]
     */
    public function getContentLines(): array
    {
        return $this->contentLines;
    }

    public function changeLineContent(int $lineNumber, string $lineContent): void
    {
        $this->contentLines[$lineNumber] = $lineContent;
    }

    public function getCurrentFileContent(): string
    {
        return implode(PHP_EOL, $this->contentLines);
    }
}
