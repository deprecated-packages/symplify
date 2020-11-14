<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tokens;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class LineResolver
{
    public function resolve(Tokens $tokens, int $position): int
    {
        $lineCount = 0;
        for ($i = 0; $i < $position; ++$i) {
            if (! isset($tokens[$i])) {
                break;
            }

            /** @var Token $token */
            $token = $tokens[$i];

            $lineCount += substr_count($token->getContent(), PHP_EOL);
        }

        return $lineCount;
    }
}
