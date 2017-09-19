<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper\Guard;

use PhpCsFixer\Tokenizer\Token;
use Symplify\CodingStandard\Exception\UnexpectedTokenException;

final class TokenTypeGuard
{
    /**
     * @param int[] $types
     */
    public static function ensureIsTokenType(Token $token, array $types, string $class): void
    {
        if ($token->isGivenKind($types)) {
            return;
        }

        throw new UnexpectedTokenException(sprintf(
            '"%s" expected "%s" token in its constructor. "%s" token given.',
            $class,
            implode(',', [$types]),
            $token->getName()
        ));
    }
}
