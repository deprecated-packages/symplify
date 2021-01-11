<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Whitespace;

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;

final class IndentResolver
{
    /**
     * @var IndentDetector
     */
    private $indentDetector;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(IndentDetector $indentDetector, WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->indentDetector = $indentDetector;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function resolveClosingBracketNewlineWhitespace(Tokens $tokens, int $startIndex): string
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $startIndex);
        return $this->whitespacesFixerConfig->getLineEnding() . str_repeat(
            $this->whitespacesFixerConfig->getIndent(),
            $indentLevel
        );
    }

    public function resolveNewlineIndentWhitespace(Tokens $tokens, int $startIndex): string
    {
        $indentWhitespace = $this->resolveIndentWhitespace($tokens, $startIndex);
        return $this->whitespacesFixerConfig->getLineEnding() . $indentWhitespace;
    }

    private function resolveIndentWhitespace(Tokens $tokens, int $startIndex): string
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $startIndex);

        return str_repeat($this->whitespacesFixerConfig->getIndent(), $indentLevel + 1);
    }
}
