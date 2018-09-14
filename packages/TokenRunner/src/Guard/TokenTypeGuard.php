<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Guard;

use PhpCsFixer\Tokenizer\Token;
use Symplify\TokenRunner\Exception\UnexpectedTokenException;
use function Safe\sprintf;

final class TokenTypeGuard
{
    /**
     * @param Token|mixed[] $token
     * @param int[] $types
     */
    public function ensureIsTokenType($token, array $types, string $location): void
    {
        if ($token instanceof Token && $token->isGivenKind($types)) {
            return;
        }

        if (is_array($token) && in_array($token['code'], $types, true)) {
            return;
        }

        $tokenNames = [];
        foreach ($types as $type) {
            $tokenNames[] = token_name($type);
        }

        throw new UnexpectedTokenException(sprintf(
            '"%s" expected "%s" token. "%s" token given.',
            $location,
            implode(',', $tokenNames),
            $token instanceof Token ? $token->getName() : $token['type']
        ));
    }
}
