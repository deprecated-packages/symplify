<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;

final class InlineVariableDocBlockMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     */
    private const SINGLE_ASTERISK_START_PATTERN = '#^/\*\s+\*(\s+@var)#';

    /**
     * @var string
     */
    private const SPACE_PATTERN = '#\s+#m';

    /**
     * @see
     * @var string
     */
    private const ASTERISK_LEFTOVERS_PATTERN = '#(\*\*)(\s+\*)#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        if (! $this->isVariableComment($tokens, $position)) {
            return $docContent;
        }

        // more than 2 newlines - keep it
        if (substr_count($docContent, StaticEolConfiguration::getEolChar()) > 2) {
            return $docContent;
        }

        // asterisk start
        $docContent = Strings::replace($docContent, self::SINGLE_ASTERISK_START_PATTERN, '/**$1');

        // inline
        $docContent = Strings::replace($docContent, self::SPACE_PATTERN, ' ');

        // remove asterisk leftover
        return Strings::replace($docContent, self::ASTERISK_LEFTOVERS_PATTERN, '$1');
    }

    private function isVariableComment(Tokens $tokens, int $position): bool
    {
        $nextPosition = $tokens->getNextMeaningfulToken($position);
        if ($nextPosition === null) {
            return false;
        }

        $nextNextPosition = $tokens->getNextMeaningfulToken($nextPosition + 2);
        if ($nextNextPosition === null) {
            return false;
        }

        /** @var Token $nextNextToken */
        $nextNextToken = $tokens[$nextNextPosition];
        if ($nextNextToken->isGivenKind([T_STATIC, T_FUNCTION])) {
            return false;
        }

        // is inline variable
        /** @var Token $nextToken */
        $nextToken = $tokens[$nextPosition];
        return $nextToken->isGivenKind(T_VARIABLE);
    }
}
