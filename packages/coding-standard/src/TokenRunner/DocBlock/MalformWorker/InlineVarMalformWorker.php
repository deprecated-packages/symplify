<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Tokens;

final class InlineVarMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     */
    private const SINGLE_ASTERISK_START_PATTERN = '#^/\*(\n?\s+@var)#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        if (! $tokens[$position]->isGivenKind(T_COMMENT)) {
            return $docContent;
        }

        return Strings::replace($docContent, self::SINGLE_ASTERISK_START_PATTERN, '/**$1');
    }
}
