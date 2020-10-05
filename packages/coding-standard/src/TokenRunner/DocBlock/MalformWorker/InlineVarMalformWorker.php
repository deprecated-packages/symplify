<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class InlineVarMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     * @see https://regex101.com/r/8OuO60/1
     */
    private const SINGLE_ASTERISK_START_REGEX = '#^/\*(\n?\s+@var)#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        /** @var Token $token */
        $token = $tokens[$position];

        if (! $token->isGivenKind(T_COMMENT)) {
            return $docContent;
        }

        return Strings::replace($docContent, self::SINGLE_ASTERISK_START_REGEX, '/**$1');
    }
}
