<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\PackageBuilder\Configuration\EolConfiguration;

final class InlineVariableDocBlockMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     */
    private const SINGLE_ASTERISK_START_PATTERN = '#^/\*\s+\*(\s+@var)#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        if (! $this->isVariableComment($tokens, $position)) {
            return $docContent;
        }

        // more than 2 newlines - keep it
        if (substr_count($docContent, EolConfiguration::getEolChar()) > 2) {
            return $docContent;
        }

        // asterisk start
        $docContent = Strings::replace($docContent, self::SINGLE_ASTERISK_START_PATTERN, '/**$1');

        // inline
        $docContent = Strings::replace($docContent, '#\s+#m', ' ');

        // remove asterisk leftover
        return Strings::replace($docContent, '#(\*\*)(\s+\*)#', '$1');
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

        if ($tokens[$nextNextPosition]->isGivenKind([T_STATIC, T_FUNCTION])) {
            return false;
        }

        // is inline variable
        return $tokens[$nextPosition]->isGivenKind(T_VARIABLE);
    }
}
