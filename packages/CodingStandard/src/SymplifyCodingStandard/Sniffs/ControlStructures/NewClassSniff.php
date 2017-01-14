<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Rules:
 * - New class statement should not have empty parentheses.
 */
final class NewClassSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.ControlStructures.NewClass';

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $openParenthesisPosition;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_NEW];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;

        if (! $this->hasEmptyParentheses()) {
            return;
        }

        $fix = $file->addFixableError('New class statement should not have empty parentheses', $position);
        if ($fix) {
            $this->removeParenthesesFromClassStatement();
        }
    }

    private function hasEmptyParentheses() : bool
    {
        $tokens = $this->file->getTokens();
        $nextPosition = $this->position;

        do {
            $nextPosition++;
        } while (! $this->doesContentContains($tokens[$nextPosition]['content'], [';', '(', ',', ')']));

        if ($tokens[$nextPosition]['content'] === '(') {
            if ($tokens[$nextPosition + 1]['content'] === ')') {
                $this->openParenthesisPosition = $nextPosition;
                return true;
            }
        }

        return false;
    }

    private function doesContentContains(string $content, array $chars) : bool
    {
        foreach ($chars as $char) {
            if ($content === $char) {
                return true;
            }
        }
        return false;
    }

    private function removeParenthesesFromClassStatement() : void
    {
        $this->file->fixer->replaceToken($this->openParenthesisPosition, '');
        $this->file->fixer->replaceToken($this->openParenthesisPosition + 1, '');
    }
}
