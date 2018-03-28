<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Configuration;

/**
 * @todo here could be added break: true, inline: true options asked by @enumag
 */
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

    public function getMaxLineLength(): int
    {
        return $this->maxLineLength;
    }
}
