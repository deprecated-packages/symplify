<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff as PHP_CodeSnifferCommentedOutCodeSniff;

/**
 * Additionally to parent check,
 * it skips single line comments - often examples
 *
 * @see \Symplify\CodingStandard\Tests\Sniffs\Debug\CommentedOutCode\CommentedOutCodeSniffTest
 */
final class CommentedOutCodeSniff extends PHP_CodeSnifferCommentedOutCodeSniff
{
    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_COMMENT];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $tokens = $file->getTokens();

        if ($this->shouldSkip($file, $position, $tokens)) {
            return;
        }

        parent::process($file, $position);
    }

    /**
     * @param mixed[] $tokens
     */
    private function shouldSkip(File $file, int $position, array $tokens): bool
    {
        // is only single line of comment in the file
        $possibleNextCommentToken = $file->findNext(T_COMMENT, $position + 1);
        if ($possibleNextCommentToken === false) {
            return true;
        }

        // is one standalone line, skip it
        return $tokens[$possibleNextCommentToken]['line'] - $tokens[$position]['line'] > 1;
    }
}
