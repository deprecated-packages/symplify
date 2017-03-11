<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ClassDeclarationSniff implements Sniff
{
    /**
     * @var string
     */
    private const OPENING_ERROR_MESSAGE = 'Opening brace for the %s should be followed by %s empty line(s); %s found.';

    /**
     * @var string
     */
    private const CLOSING_ERROR_MESSAGE = 'Closing brace for the %s should be preceded by %s empty line(s); %s found.';

    /**
     * @var int
     */
    public $emptyLinesAfterOpeningBrace = 0;

    /**
     * @var int
     */
    public $emptyLinesBeforeClosingBrace = 0;

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array[]
     */
    private $tokens;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS, T_INTERFACE, T_TRAIT];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        if ($this->isEmptyClass()) {
            return;
        }

        $this->processOpen();
        $this->processClose();
    }

    private function processOpen(): void
    {
        $openingBracePosition = $this->tokens[$this->position]['scope_opener'];
        $emptyLinesCount = $this->getEmptyLinesAfterOpeningBrace($openingBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesAfterOpeningBrace) {
            $errorMessage = sprintf(
                self::OPENING_ERROR_MESSAGE,
                $this->tokens[$this->position]['content'],
                $this->emptyLinesAfterOpeningBrace,
                $emptyLinesCount
            );

            $fix = $this->file->addFixableError($errorMessage, $openingBracePosition, self::class);
            if ($fix) {
                $this->fixOpeningBraceSpaces($openingBracePosition, $emptyLinesCount);
            }
        }
    }

    private function processClose(): void
    {
        $closingBracePosition = $this->tokens[$this->position]['scope_closer'];
        $emptyLinesCount = $this->getEmptyLinesBeforeClosingBrace($closingBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesBeforeClosingBrace) {
            $errorMessage = sprintf(
                self::CLOSING_ERROR_MESSAGE,
                $this->tokens[$this->position]['content'],
                $this->emptyLinesBeforeClosingBrace,
                $emptyLinesCount
            );

            $fix = $this->file->addFixableError($errorMessage, $closingBracePosition, self::class);
            if ($fix) {
                $this->fixClosingBraceSpaces($closingBracePosition, $emptyLinesCount);
            }
        }
    }

    private function getEmptyLinesBeforeClosingBrace(int $position): int
    {
        $prevContent = $this->file->findPrevious(T_WHITESPACE, ($position - 1), null, true);

        return $this->tokens[$position]['line'] - $this->tokens[$prevContent]['line'] - 1;
    }

    private function getEmptyLinesAfterOpeningBrace(int $position): int
    {
        $nextContent = $this->file->findNext(T_WHITESPACE, ($position + 1), null, true);

        return $this->tokens[$nextContent]['line'] - $this->tokens[$position]['line'] - 1;
    }

    private function fixOpeningBraceSpaces(int $position, int $numberOfSpaces): void
    {
        if ($numberOfSpaces < $this->emptyLinesAfterOpeningBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesAfterOpeningBrace; ++$i) {
                $this->file->fixer->addContent($position, PHP_EOL);
            }
        } elseif ($numberOfSpaces > $this->emptyLinesAfterOpeningBrace) {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesAfterOpeningBrace; --$i) {
                $this->file->fixer->replaceToken($position + $i, '');
            }
        }
    }

    private function fixClosingBraceSpaces(int $position, int $numberOfSpaces): void
    {
        if ($numberOfSpaces < $this->emptyLinesBeforeClosingBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesBeforeClosingBrace; ++$i) {
                $this->file->fixer->addContentBefore($position, PHP_EOL);
            }
        } elseif ($numberOfSpaces > $this->emptyLinesBeforeClosingBrace) {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesBeforeClosingBrace; --$i) {
                $this->file->fixer->replaceToken($position - $i, '');
            }
        }
    }

    private function isEmptyClass(): bool
    {
        return $this->getEmptyLinesBetweenOpenerAndCloser() < 2;
    }

    private function getEmptyLinesBetweenOpenerAndCloser(): int
    {
        $openingBracePosition = $this->tokens[$this->position]['scope_opener'];
        $closingBracePosition = $this->tokens[$this->position]['scope_closer'];

        $openingBraceToken = $this->tokens[$openingBracePosition];
        $closingBraceToken = $this->tokens[$closingBracePosition];

        return $closingBraceToken['line'] - $openingBraceToken['line'] - 1;
    }
}
