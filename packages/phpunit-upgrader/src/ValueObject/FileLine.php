<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\ValueObject;

final class FileLine
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var int
     */
    private $line;

    public function __construct(string $filePath, int $line)
    {
        $this->filePath = $filePath;
        $this->line = $line;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
