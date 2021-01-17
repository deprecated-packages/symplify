<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenAnalyzer;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NewlineAnalyzer
{
    public function doesContentBeforeBracketRequireNewline(Tokens $tokens, int $i): bool
    {
        $previousMeaningfulTokenPosition = $tokens->getPrevNonWhitespace($i);
        if ($previousMeaningfulTokenPosition === null) {
            return false;
        }

        $previousToken = $tokens[$previousMeaningfulTokenPosition];
        if (! $previousToken->isGivenKind(T_STRING)) {
            return false;
        }

        $previousPreviousMeaningfulTokenPosition = $tokens->getPrevNonWhitespace($previousMeaningfulTokenPosition);
        if ($previousPreviousMeaningfulTokenPosition === null) {
            return false;
        }

        $previousPreviousToken = $tokens[$previousPreviousMeaningfulTokenPosition];
        if ($previousPreviousToken->getContent() === '{') {
            return true;
        }

        // is a function
        return $previousPreviousToken->isGivenKind([T_RETURN, T_DOUBLE_COLON, T_OPEN_CURLY_BRACKET]);
    }

    public function isNewlineToken(Token $currentToken): bool
    {
        if (! $currentToken->isWhitespace()) {
            return false;
        }

        return Strings::contains($currentToken->getContent(), "\n");
    }
}
