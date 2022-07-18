<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class SkipTokens
{
    /**
     * @param Tokens<Token> $tokens
     */
    public function run(Tokens $tokens)
    {
        $tokens->ensureWhitespaceAtIndex(1, 2, '');
    }
}
