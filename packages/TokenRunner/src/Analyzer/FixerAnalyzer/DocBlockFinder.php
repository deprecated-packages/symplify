<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Tokens;

final class DocBlockFinder
{
    public function findPreviousPosition(Tokens $tokens, int $index): ?int
    {
        for ($i = $index; $i > 0; --$i) {
            $token = $tokens[$i];

            if ($token->getContent() === ';') {
                return null;
            }

            // another block starts -> skip
            if ($token->getContent() === '}') {
                return null;
            }

            if ($token->isComment()) {
                return $i;
            }
        }

        return null;
    }
}
