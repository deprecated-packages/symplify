<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\EasyCodingStandard\Parallel\Contract\Serializable;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Name;

final class FileDiff implements Serializable
{
    /**
     * @param string[] $appliedCheckers
     */
    public function __construct(
        private string $relativeFilePath,
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

    public function getRelativeFilePath(): string
    {
        return $this->relativeFilePath;
    }

    /**
     * @return array{Name::DIFF: string, Name::DIFF_CONSOLE_FORMATTED: string, Name::APPLIED_CHECKERS: string[], Name::RELATIVE_FILE_PATH: string}
     */
    public function jsonSerialize(): array
    {
        return [
            Name::DIFF => $this->diff,
            Name::DIFF_CONSOLE_FORMATTED => $this->consoleFormattedDiff,
            Name::APPLIED_CHECKERS => $this->getAppliedCheckers(),
            Name::RELATIVE_FILE_PATH => $this->relativeFilePath,
        ];
    }

    /**
     * @param array{Name::DIFF: string, Name::DIFF_CONSOLE_FORMATTED: string, Name::APPLIED_CHECKERS: string[], Name::RELATIVE_FILE_PATH: string} $json
     */
    public static function decode(array $json): self
    {
        return new self(
            $json[NAME::DIFF],
            $json[NAME::DIFF_CONSOLE_FORMATTED],
            $json[NAME::APPLIED_CHECKERS],
            $json[NAME::RELATIVE_FILE_PATH],
        );
    }
}
