<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Tokens;

use Nette\Utils\Strings;

final class PhpToLatteLineNumbersResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/Qb4cuo/1
     */
    private const COMMENTED_LINE_NUMBER_REGEX = '#^\/\* line (?<' . self::NUMBER_KEY . '>\d+) \*\/$#';

    /**
     * @var string
     */
    private const NUMBER_KEY = 'number';

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

            $lineMatch = Strings::match($token[1], self::COMMENTED_LINE_NUMBER_REGEX);
            if (! isset($lineMatch[self::NUMBER_KEY])) {
                continue;
            }

            $phpLineNumber = $this->resolveLineNumberFromTokensOnPosition($tokens, $position);
            // correct the line number by number of added var types
            $phpLineNumber += $variablesAndTypesCount - 1;
            $latteLineNumber = (int) $lineMatch[self::NUMBER_KEY];

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
