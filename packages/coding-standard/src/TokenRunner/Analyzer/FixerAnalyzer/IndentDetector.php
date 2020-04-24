<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

final class IndentDetector
{
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function detectOnPosition(Tokens $tokens, int $startIndex): int
    {
        $indent = $this->whitespacesFixerConfig->getIndent();

        for ($i = $startIndex; $i > 0; --$i) {
            $token = $tokens[$i];

            $lastNewlinePos = strrpos($token->getContent(), "\n");

            if ($token->isWhitespace() && $token->getContent() !== ' ' || $lastNewlinePos !== false) {
                return substr_count($token->getContent(), $indent, (int) $lastNewlinePos);
            }
        }

        return 0;
    }
}
