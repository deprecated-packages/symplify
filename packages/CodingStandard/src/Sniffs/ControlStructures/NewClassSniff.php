<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;

final class NewClassSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'New class statement should not have empty parentheses.';

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $openParenthesisPosition;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->fixer = $file->fixer;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        if (! $this->hasEmptyParentheses()) {
            return;
        }

        $fix = $file->addFixableError(self::ERROR_MESSAGE, $position, self::class);
        if ($fix) {
            $this->removeParenthesesFromClassStatement();
        }
    }

    private function hasEmptyParentheses(): bool
    {
        $nextPosition = $this->position;

        do {
            ++$nextPosition;
        } while (! $this->doesContentContains($this->tokens[$nextPosition]['content'], [';', '(', ',', ')']));

        if ($this->tokens[$nextPosition]['content'] === '(') {
            if ($this->tokens[$nextPosition + 1]['content'] === ')') {
                $this->openParenthesisPosition = $nextPosition;

                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $chars
     */
    private function doesContentContains(string $content, array $chars): bool
    {
        foreach ($chars as $char) {
            if ($content === $char) {
                return true;
            }
        }

        return false;
    }

    private function removeParenthesesFromClassStatement(): void
    {
        $this->fixer->replaceToken($this->openParenthesisPosition, '');
        $this->fixer->replaceToken($this->openParenthesisPosition + 1, '');
    }
}
