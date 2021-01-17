<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ChainMethodCallAnalyzer
{
    /**
     * @var NewlineAnalyzer
     */
    private $newlineAnalyzer;

    public function __construct(NewlineAnalyzer $newlineAnalyzer)
    {
        $this->newlineAnalyzer = $newlineAnalyzer;
    }

    /**
     * Matches e..g:
     * - return app()->some()
     * - app()->some()
     * - (clone app)->some()
     */
    public function isPreceededByFuncCall(Tokens $tokens, int $position): bool
    {
        for ($i = $position; $i >= 0; --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];

            if ($currentToken->getContent() === 'clone') {
                return true;
            }

            if ($currentToken->getContent() === '(') {
                return $this->newlineAnalyzer->doesContentBeforeBracketRequireNewline($tokens, $i);
            }

            if ($this->newlineAnalyzer->isNewlineToken($currentToken)) {
                return false;
            }
        }

        return false;
    }
}
