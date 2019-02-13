<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Printer;

use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Lexer\Lexer;

final class OriginalSpacingRestorer
{
    /**
     * @param mixed[] $tokens
     */
    public function restoreInOutputWithTokensStartAndEndPosition(
        string $nodeOutput,
        array $tokens,
        int $startTokenPosition,
        int $endTokenPosition
    ): string {
        $oldWhitespaces = $this->detectOldWhitespaces($tokens, $startTokenPosition, $endTokenPosition);

        // no original whitespaces, return
        if (! $oldWhitespaces) {
            return $nodeOutput;
        }

        $newNodeOutput = '';
        $i = 0;

        // replace system whitespace by old ones
        foreach (Strings::split($nodeOutput, '#\s+#') as $nodeOutputPart) {
            $newNodeOutput .= ($oldWhitespaces[$i] ?? '') . $nodeOutputPart;
            ++$i;
        }

        // remove first space, added by the printer above
        return Strings::substring($newNodeOutput, 1);
    }

    /**
     * @param mixed[] $tokens
     * @return string[]
     */
    private function detectOldWhitespaces(array $tokens, int $startTokenPosition, int $endTokenPosition): array
    {
        $oldWhitespaces = [];

        for ($i = $startTokenPosition; $i < $endTokenPosition; ++$i) {
            if ($tokens[$i][1] === Lexer::TOKEN_HORIZONTAL_WS) {
                $oldWhitespaces[] = $tokens[$i][0];
            }

            // quoted string with spaces?
            if (in_array(
                $tokens[$i][1],
                [Lexer::TOKEN_SINGLE_QUOTED_STRING, Lexer::TOKEN_DOUBLE_QUOTED_STRING],
                true
            )) {
                $matches = Strings::matchAll($tokens[$i][0], '#\s+#m');
                if ($matches) {
                    $oldWhitespaces = array_merge($oldWhitespaces, Arrays::flatten($matches));
                }
            }
        }

        return $oldWhitespaces;
    }
}
