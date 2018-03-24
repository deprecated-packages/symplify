<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ClassNameFinder
{
    public function findInTokens(Tokens $tokens): string
    {
        $classPosition = $tokens->getNextTokenOfKind(0, [new Token([T_CLASS, 'class'])]);
        $classNamePosition = $tokens->getNextMeaningfulToken($classPosition);

        return $tokens[$classNamePosition]->getContent();
    }
}
