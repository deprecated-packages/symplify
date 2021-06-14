<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tokens;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\ValueObject\StartAndEnd;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * Heavily inspired by
 *
 * @see https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php
 */
final class CommentedContentResolver
{
    /**
     * @var int[]
     */
    public const EMPTY_TOKENS = [T_WHITESPACE, T_STRING, T_ENCAPSED_AND_WHITESPACE, T_COMMENT];

    public function __construct(
        private LineResolver $lineResolver
    ) {
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function resolve(Tokens $tokens, int $position): StartAndEnd
    {
        $token = $tokens[$position];
        if (! $token->isGivenKind(T_COMMENT)) {
            throw new ShouldNotHappenException();
        }

        $lastLineSeen = $this->lineResolver->resolve($tokens, $position);
        $startPosition = $position;
        $lastPosition = $position;

        // content after token
        for ($i = $position; $i < $tokens->count(); ++$i) {
            /** @var Token $token */
            $token = $tokens[$i];

            if (! $token->isGivenKind(self::EMPTY_TOKENS)) {
                continue;
            }

            if ($token->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $tokenLine = $this->lineResolver->resolve($tokens, $i);

            if ($this->shouldBreak($lastLineSeen, $tokenLine, $token)) {
                break;
            }

            $lastPosition = $i;
            $lastLineSeen = $tokenLine;

            // Trim as much off the comment as possible so we don't, have additional whitespace tokens or comment tokens
            $tokenContent = trim($token->getContent());
            $hasBlockCommentCloser = \str_ends_with($tokenContent, '*/');

            if ($hasBlockCommentCloser) {
                // Closer of a block comment found
                break;
            }
        }

        return new StartAndEnd($startPosition, $lastPosition);
    }

    private function shouldBreak(int $lastLineSeen, int $tokenLine, Token $token): bool
    {
        if ($lastLineSeen + 1 <= $tokenLine
            && \str_starts_with($token->getContent(), '/*')
        ) {
            // First non-whitespace token on a new line is start of a different style comment.
            return true;
        }

        if ($this->isNextLineNotComment($lastLineSeen, $tokenLine, $token)) {
            return true;
        }

        // Blank line breaks a '//' style comment block.
        return $lastLineSeen + 1 < $tokenLine;
    }

    private function isNextLineNotComment(int $lastLineSeen, int $tokenLine, Token $token): bool
    {
        if ($lastLineSeen >= $tokenLine) {
            return false;
        }

        return ! \str_starts_with($token->getContent(), '//');
    }
}
