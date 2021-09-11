<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte\Tokens;

use Nette\Utils\Strings;

final class PhpToLatteLineNumbersResolver
{
    /**
     * @param array<int, mixed> $tokens
     * @return array<int, int>
     */
    public function resolve(array $tokens, int $variablesAndTypesCount): array
    {
        $phpLinesToLatteLines = [];

        foreach ($tokens as $position => $token) {
            // 388
            if ($token[0] !== T_COMMENT) {
                continue;
            }

            $lineMatch = Strings::match($token[1], '#^\/\* line (?<number>\d+) \*\/$#');
            if (! isset($lineMatch['number'])) {
                continue;
            }

            $phpLineNumber = $this->resolveLineNumberFromTokensOnPosition($tokens, $position);
            // correct the line number by number of added var types
            $phpLineNumber += $variablesAndTypesCount - 1;
            $latteLineNumber = (int) $lineMatch['number'];

            $phpLinesToLatteLines[$phpLineNumber] = $latteLineNumber;
        }

        return $phpLinesToLatteLines;
    }

    private function resolveLineNumberFromTokensOnPosition(array $tokens, int $desiredPosition): int
    {
        $lineNumber = 0;

        foreach ($tokens as $position => $token) {
            $lineNumber += substr_count($token[1], "\n");
            if ($position === $desiredPosition) {
                break;
            }
        }

        return $lineNumber;
    }
}
