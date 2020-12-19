<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Php;

use Symplify\CodingStandard\TokenRunner\TokenFinder;
use Symplify\CodingStandard\Tokens\CommentedContentResolver;

final class PhpContentAnalyzer
{
    /**
     * @var TokenFinder
     */
    private $tokenFinder;

    public function __construct(TokenFinder $tokenFinder)
    {
        $this->tokenFinder = $tokenFinder;
    }

    public function isPhpContent(string $content): bool
    {
        // is content commented PHP code?
        $rawTokens = $this->parseCodeToTokens($content);
        $tokenCount = count($rawTokens);

        // probably not content
        if ($tokenCount < 3) {
            return false;
        }

        // has 2 strings after each other, not PHP code
        if ($this->hasTwoStringsTokensInRow($tokenCount, $rawTokens)) {
            return false;
        }

        $firstInLineLintedCorrectly = false;

        for ($i = 0; $i < $tokenCount; ++$i) {
            $rawToken = $rawTokens[$i];

            // twig
            if ($rawToken === '{') {
                $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                if ($nextToken === '%') {
                    return false;
                }
            }

            if (! isset($rawToken[2])) {
                continue;
            }

            if ($firstInLineLintedCorrectly === false) {
                if ($rawToken[0] === T_CONSTANT_ENCAPSED_STRING) {
                    return false;
                }

                if ($rawToken[0] === T_FOR || $rawToken[0] === T_DO) {
                    $lastLineToken = $this->tokenFinder->getSameRowLastToken($rawTokens, $i + 1);
                    if ($lastLineToken !== '{') {
                        return false;
                    }
                }

                if ($rawToken[0] === T_INCLUDE || $rawToken[0] === T_EMPTY) {
                    $lastLineToken = $this->tokenFinder->getSameRowLastToken($rawTokens, $i + 1);
                    if ($lastLineToken !== ';') {
                        return false;
                    }
                }

                if ($rawToken[0] === T_DEFAULT) {
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if (! is_array($nextToken)) {
                        return false;
                    }

                    if ($nextToken !== ':') {
                        return false;
                    }
                }

                // token id: 311
                if ($rawToken[0] === T_STRING) {
                    return false;
                }

                if ($rawToken[0] === T_NAMESPACE) {
                    // is namespace part
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if (! is_array($nextToken)) {
                        return false;
                    }
                }

                if ($rawToken[0] === T_VARIABLE) {
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if ($nextToken === [] || ! is_array($nextToken)) {
                        return false;
                    }

                    if ($nextToken[0] === T_STRING) {
                        return false;
                    }
                }
            }

            if ($rawToken[0] === T_FUNCTION) {
                if (! $this->isFunctionStart($rawTokens, $i)) {
                    return false;
                }
            }

            if ($firstInLineLintedCorrectly === false) {
                $firstInLineLintedCorrectly = true;
            }

            // is comment content
            if (in_array($rawToken[0], CommentedContentResolver::EMPTY_TOKENS, true)) {
                continue;
            }

            // new line comming next → restart string check
            if ($rawToken[1] === PHP_EOL) {
                $firstInLineLintedCorrectly = false;
            }
        }

        return true;
    }

    /**
     * @param mixed[] $tokens
     */
    private function isFunctionStart(array $tokens, int $i): bool
    {
        $twoNextTokens = $this->tokenFinder->getNextMeaninfulTokens($tokens, $i + 1, 2);
        if (count($twoNextTokens) !== 2) {
            return false;
        }

        $nameToken = $twoNextTokens[0];
        $openBracketToken = $twoNextTokens[1];
        if ($nameToken[0] !== T_STRING) {
            return false;
        }

        return $openBracketToken === '(';
    }

    private function hasTwoStringsTokensInRow(int $tokenCount, array $rawTokens): bool
    {
        for ($i = 0; $i < $tokenCount; ++$i) {
            $token = $rawTokens[$i];
            if ($token[0] !== T_STRING) {
                continue;
            }

            $nextTokens = $this->tokenFinder->getNextMeaninfulTokens($rawTokens, $i + 1, 1);
            if ($nextTokens === []) {
                continue;
            }

            if ($nextTokens[0][0] !== T_STRING) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function parseCodeToTokens(string $content): array
    {
        $phpContent = '<?php ' . PHP_EOL . $content;
        $rawTokens = token_get_all($phpContent);

        return array_slice($rawTokens, 2);
    }
}
