<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class MissingVarNameMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     * @see https://regex101.com/r/QtWnWv/3
     */
    private const VAR_WITHOUT_NAME_REGEX = '#^(?<open>\/\*\* @var )(?<type>[\\\\\w\|]+)(?<close>\s+\*\/)$#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        if (! Strings::match($docContent, self::VAR_WITHOUT_NAME_REGEX)) {
            return $docContent;
        }

        $nextVariableToken = $this->getNextVariableToken($tokens, $position);
        if ($nextVariableToken === null) {
            return $docContent;
        }

        return Strings::replace($docContent, self::VAR_WITHOUT_NAME_REGEX, function (array $match) use (
            $nextVariableToken
        ): string {
            return $match['open'] . $match['type'] . ' ' . $nextVariableToken->getContent() . $match['close'];
        });
    }

    private function getNextVariableToken(Tokens $tokens, int $position): ?Token
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($position);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }

        $nextToken = $tokens[$nextMeaningfulTokenPosition] ?? null;
        if (! $nextToken instanceof Token) {
            return null;
        }

        if (! $nextToken->isGivenKind(T_VARIABLE)) {
            return null;
        }

        return $nextToken;
    }
}
