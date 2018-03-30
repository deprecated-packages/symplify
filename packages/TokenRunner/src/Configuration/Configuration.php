<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Configuration;

use PhpCsFixer\WhitespacesFixerConfig;

final class Configuration
{
    /**
     * @var int
     */
    private $maxLineLength;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var bool
     */
    private $breakLongLines = false;

    /**
     * @var bool
     */
    private $inlineShortLines = false;

    public function __construct(
        int $maxLineLength,
        bool $breakLongLines,
        bool $inlineShortLines,
        WhitespacesFixerConfig $whitespacesFixerConfig
    ) {
        $this->maxLineLength = $maxLineLength;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->breakLongLines = $breakLongLines;
        $this->inlineShortLines = $inlineShortLines;
    }

    public function shouldBreakLongLines(): bool
    {
        return $this->breakLongLines;
    }

    public function shouldInlineShortLines(): bool
    {
        return $this->inlineShortLines;
    }

    public function getMaxLineLength(): int
    {
        return $this->maxLineLength;
    }

    public function getIndent(): string
    {
        return $this->whitespacesFixerConfig->getIndent();
    }

    public function getLineEnding(): string
    {
        return $this->whitespacesFixerConfig->getLineEnding();
    }
}
