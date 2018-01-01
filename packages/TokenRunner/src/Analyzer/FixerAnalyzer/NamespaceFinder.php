<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Tokens;

final class NamespaceFinder
{
    public static function findInTokens(Tokens $tokens): ?int
    {
        $namespace = $tokens->findGivenKind(T_NAMESPACE);
        reset($namespace);

        return key($namespace);
    }
}
