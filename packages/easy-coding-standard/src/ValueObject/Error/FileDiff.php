<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\SmartFileSystem\SmartFileInfo;

final class FileDiff
{
    /**
     * @param string[] $appliedCheckers
     */
    public function __construct(
        private SmartFileInfo $smartFileInfo,
        private string $diff,
        private string $consoleFormattedDiff,
        private array $appliedCheckers
    ) {
    }

    public function getDiff(): string
    {
        return $this->diff;
    }

    public function getDiffConsoleFormatted(): string
    {
        return $this->consoleFormattedDiff;
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckers(): array
    {
        $this->appliedCheckers = array_unique($this->appliedCheckers);
        sort($this->appliedCheckers);

        return $this->appliedCheckers;
    }

    public function getRelativeFilePathFromCwd(): string
    {
        return $this->smartFileInfo->getRelativeFilePathFromCwd();
    }
}
