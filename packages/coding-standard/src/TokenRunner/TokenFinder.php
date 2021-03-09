<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class TokenFinder
{
    /**
     * @param int|Token $position
     * @param Tokens<Token> $tokens
     */
    public function getPreviousMeaningfulToken(Tokens $tokens, $position): Token
    {
        if (is_int($position)) {
            return $this->findPreviousTokenByPosition($tokens, $position);
        }

        return $this->findPreviousTokenByToken($tokens, $position);
    }

    /**
     * @param mixed[] $tokens
     * @return mixed[]|string|null
     */
    public function getNextMeaninfulToken(array $tokens, int $position)
    {
        $tokens = $this->getNextMeaninfulTokens($tokens, $position, 1);
        return $tokens[0] ?? null;
    }

    /**
     * @param mixed[] $tokens
     * @return mixed[]
     */
    public function getNextMeaninfulTokens(array $tokens, int $position, int $count): array
    {
        $foundTokens = [];
        $tokensCount = count($tokens);
        for ($i = $position; $i < $tokensCount; ++$i) {
            $token = $tokens[$i];
            if ($token[0] === T_WHITESPACE) {
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
     * @param mixed[] $rawTokens
     * @return mixed[]|string
     */
    public function getSameRowLastToken(array $rawTokens, int $position)
    {
        $lastToken = null;
        $rawTokensCount = count($rawTokens);
        for ($i = $position; $i < $rawTokensCount; ++ $i) {
            $token = $rawTokens[$i];

            if (is_array($token) && Strings::contains($token[1], PHP_EOL)) {
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

        $previousToken = $tokens[$previousPosition];
        if (! $previousToken instanceof Token) {
            throw new ShouldNotHappenException();
        }

        return $previousToken;
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
