<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff as PHP_CodeSnifferCommentedOutCodeSniff;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Additionally to parent check,
 * it skips single line comments - often examples
 *
 * @see \Symplify\CodingStandard\Tests\Sniffs\Debug\CommentedOutCode\CommentedOutCodeSniffTest
 */
final class CommentedOutCodeSniff extends PHP_CodeSnifferCommentedOutCodeSniff implements DocumentedRuleInterface
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('There should be no commented code. Git is good enough for versioning', [
            new CodeSample(
                <<<'CODE_SAMPLE'
// $one = 1;
// $two = 2;
// $three = 3;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// note
CODE_SAMPLE
            ),
        ]);
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
