<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Configuration;

final class Configuration
{
    /**
     * @var int
     */
    private $maxLineLength;

    public function __construct(int $maxLineLength)
    {
        $this->maxLineLength = $maxLineLength;
    }

    public function getMaxLineLenght(): int
    {
        return $this->maxLineLength;
    }
}
