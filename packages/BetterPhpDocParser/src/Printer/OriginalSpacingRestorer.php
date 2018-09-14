<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Printer;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Lexer\Lexer;
use Symplify\BetterPhpDocParser\PhpDocNodeInfo;
use function Safe\substr;

final class OriginalSpacingRestorer
{
    /**
     * @param mixed[] $tokens
     */
    public function restoreInOutputWithTokensAndPhpDocNodeInfo(
        string $nodeOutput,
        array $tokens,
        PhpDocNodeInfo $phpDocNodeInfo
    ): string {
        $oldWhitespaces = [];
        for ($i = $phpDocNodeInfo->getStart(); $i < $phpDocNodeInfo->getEnd(); ++$i) {
            if ($tokens[$i][1] === Lexer::TOKEN_HORIZONTAL_WS) {
                $oldWhitespaces[] = $tokens[$i][0];
            }
        }

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
        return substr($newNodeOutput, 1);
    }
}
