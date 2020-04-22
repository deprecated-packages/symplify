<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Tokens;

final class DocBlockFinder
{
    public function findPreviousPosition(Tokens $tokens, int $index): ?int
    {
        for ($i = $index; $i > 0; --$i) {
            $token = $tokens[$i];

            if ($token->equals(';')) {
                return null;
            }

            // another block starts -> skip
            if ($token->equals('}')) {
                return null;
            }

            if ($token->isComment()) {
                return $i;
            }
        }

        return null;
    }
}
