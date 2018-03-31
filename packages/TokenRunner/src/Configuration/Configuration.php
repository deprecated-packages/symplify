<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Configuration;

use PhpCsFixer\WhitespacesFixerConfig;

final class Configuration
{
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
        bool $breakLongLines,
        bool $inlineShortLines,
        WhitespacesFixerConfig $whitespacesFixerConfig
    ) {
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

    public function getIndent(): string
    {
        return $this->whitespacesFixerConfig->getIndent();
    }

    public function getLineEnding(): string
    {
        return $this->whitespacesFixerConfig->getLineEnding();
    }
}
