<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpToken;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class TokenFinder
{
    /**
     * @param Tokens<Token> $tokens
     */
    public function getPreviousMeaningfulToken(Tokens $tokens, int | Token $position): Token
    {
        if (is_int($position)) {
            return $this->findPreviousTokenByPosition($tokens, $position);
        }

        return $this->findPreviousTokenByToken($tokens, $position);
    }

    /**
     * @param PhpToken[] $tokens
     */
    public function getNextMeaninfulToken(array $tokens, int $position): PhpToken | null
    {
        $tokens = $this->getNextMeaninfulTokens($tokens, $position, 1);
        return $tokens[0] ?? null;
    }

    /**
     * @param PhpToken[] $tokens
     * @return PhpToken[]
     */
    public function getNextMeaninfulTokens(array $tokens, int $position, int $count): array
    {
        $foundTokens = [];
        $tokensCount = count($tokens);
        for ($i = $position; $i < $tokensCount; ++$i) {
            $token = $tokens[$i];
            if ($token->is(T_WHITESPACE)) {
                continue;
            }

            if (count($foundTokens) === $count) {
                break;
            }

            $foundTokens[] = $token;
        }

        return $foundTokens;
    }

    /**
     * @param PhpToken[] $rawTokens
     */
    public function getSameRowLastToken(array $rawTokens, int $position): ?PhpToken
    {
        $lastToken = null;
        $rawTokensCount = count($rawTokens);
        for ($i = $position; $i < $rawTokensCount; ++$i) {
            $token = $rawTokens[$i];
            if (\str_contains($token->text, PHP_EOL)) {
                break;
            }

            $lastToken = $token;
        }

        return $lastToken;
    }

    /**
     * @param Tokens<Token> $tokens
     */
    private function findPreviousTokenByPosition(Tokens $tokens, int $position): Token
    {
        $previousPosition = $position - 1;
        if (! isset($tokens[$previousPosition])) {
            throw new ShouldNotHappenException();
        }

        return $tokens[$previousPosition];
    }

    /**
     * @param Tokens<Token> $tokens
     */
    private function findPreviousTokenByToken(Tokens $tokens, Token $positionToken): Token
    {
        $position = $this->resolvePositionByToken($tokens, $positionToken);
        return $this->findPreviousTokenByPosition($tokens, $position - 1);
    }

    /**
     * @param Tokens<Token> $tokens
     */
    private function resolvePositionByToken(Tokens $tokens, Token $positionToken): int
    {
        foreach ($tokens as $position => $token) {
            if ($token === $positionToken) {
                return $position;
            }
        }

        throw new ShouldNotHappenException();
    }
}
