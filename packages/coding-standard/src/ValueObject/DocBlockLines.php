<?php
declare(strict_types=1);

namespace Symplify\CodingStandard\ValueObject;

final class DocBlockLines
{
    /**
     * @var array<string>
     */
    private $descriptionLines;

    /**
     * @var array<string>
     */
    private $otherLines;

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
    public function descriptionLines(): array
    {
        return $this->descriptionLines;
    }

    /**
     * @return array<string>
     */
    public function otherLines(): array
    {
        return $this->otherLines;
    }
}
