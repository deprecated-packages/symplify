<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Guard;

use PhpCsFixer\Tokenizer\Token;
use Symplify\TokenRunner\Exception\UnexpectedTokenException;

/**
 * @todo make univesal for both!!!
 */
final class TokenTypeGuard
{
    /**
     *
     * @parma Token|mixed[] $token
     * @param int[] $types
     */
    public static function ensureIsTokenType($token, array $types, string $location): void
    {
        if ($token instanceof Token && $token->isGivenKind($types)) {
            return;
        }

        if (is_array($token) && in_array($token['type'], $types, true)) {
            return;
        }

        throw new UnexpectedTokenException(sprintf(
            '"%s" expected "%s" token. "%s" token given.',
            $location,
            implode(',', [$types]),
            $token instanceof Token ? $token->getName() : $token['name']
        ));
    }
}
