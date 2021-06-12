<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ValueObject;

final class DocBlockLines
{
    /**
     * @var array<string>
     */
    private array $descriptionLines = [];

    /**
     * @var array<string>
     */
    private array $otherLines = [];

    /**
     * @param array<string> $descriptionLines
     * @param array<string> $otherLines
     */
    public function __construct(array $descriptionLines, array $otherLines)
    {
        $this->descriptionLines = $descriptionLines;
        $this->otherLines = $otherLines;
    }

    /**
     * @return array<string>
     */
    public function getDescriptionLines(): array
    {
        return $this->descriptionLines;
    }

    /**
     * @return array<string>
     */
    public function getOtherLines(): array
    {
        return $this->otherLines;
    }

    public function hasListDescriptionLines(): bool
    {
        foreach ($this->descriptionLines as $descriptionLine) {
            if (\str_starts_with($descriptionLine, '-')) {
                return true;
            }
        }

        return false;
    }
}
