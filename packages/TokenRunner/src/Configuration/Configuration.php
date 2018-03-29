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

    public function __construct(int $maxLineLength, WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->maxLineLength = $maxLineLength;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
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
