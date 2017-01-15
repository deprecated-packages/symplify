<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Standards\PEAR\Sniffs\Classes\ClassDeclarationSniff as PearClassDeclarationSniff;
use PHP_CodeSniffer\Files\File;

/**
 * Rules (new to parent class):
 * - Opening brace for the %s should be followed by %s empty line(s).
 * - Closing brace for the %s should be preceded by %s empty line(s).
 */
final class ClassDeclarationSniff extends PearClassDeclarationSniff
{
    /**
     * @var string
     */
    public const NAME = 'Symplify\CodingStandard.Classes.ClassDeclaration';

    /**
     * @var int|string
     */
    public $emptyLinesAfterOpeningBrace = 0;

    /**
     * @var int|string
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
     * @var array
     */
    private $tokens;

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        parent::process($file, $position);
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        // Fix type
        $this->emptyLinesAfterOpeningBrace = (int) $this->emptyLinesAfterOpeningBrace;
        $this->emptyLinesBeforeClosingBrace = (int) $this->emptyLinesBeforeClosingBrace;

        $this->processOpen();
        $this->processClose();
    }

    private function processOpen() : void
    {
        $openingBracePosition = $this->tokens[$this->position]['scope_opener'];
        $emptyLinesCount = $this->getEmptyLinesAfterOpeningBrace($openingBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesAfterOpeningBrace) {
            $errorMessage = sprintf(
                'Opening brace for the %s should be followed by %s empty line(s); %s found.',
                $this->tokens[$this->position]['content'],
                $this->emptyLinesAfterOpeningBrace,
                $emptyLinesCount
            );

            $fix = $this->file->addFixableError($errorMessage, $openingBracePosition, null);
            if ($fix) {
                $this->fixOpeningBraceSpaces($openingBracePosition, $emptyLinesCount);
            }
        }
    }

    private function processClose() : void
    {
        $closeBracePosition = $this->tokens[$this->position]['scope_closer'];
        $emptyLinesCount = $this->getEmptyLinesBeforeClosingBrace($closeBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesBeforeClosingBrace) {
            $errorMessage = sprintf(
                'Closing brace for the %s should be preceded by %s empty line(s); %s found.',
                $this->tokens[$this->position]['content'],
                $this->emptyLinesBeforeClosingBrace,
                $emptyLinesCount
            );

            $fix = $this->file->addFixableError($errorMessage, $closeBracePosition, null);
            if ($fix) {
                $this->fixClosingBraceSpaces($closeBracePosition, $emptyLinesCount);
            }
        }
    }

    private function getEmptyLinesBeforeClosingBrace(int $position) : int
    {
        $prevContent = $this->file->findPrevious(T_WHITESPACE, ($position - 1), null, true);
        return $this->tokens[$position]['line'] - $this->tokens[$prevContent]['line'] - 1;
    }

    private function getEmptyLinesAfterOpeningBrace(int $position) : int
    {
        $nextContent = $this->file->findNext(T_WHITESPACE, ($position + 1), null, true);
        return $this->tokens[$nextContent]['line'] - $this->tokens[$position]['line'] - 1;
    }

    private function fixOpeningBraceSpaces(int $position, int $numberOfSpaces) : void
    {
        if ($numberOfSpaces < $this->emptyLinesAfterOpeningBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesAfterOpeningBrace; $i++) {
                $this->file->fixer->addContent($position, PHP_EOL);
            }
        } elseif ($numberOfSpaces > $this->emptyLinesAfterOpeningBrace) {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesAfterOpeningBrace; $i--) {
                $this->file->fixer->replaceToken($position + $i, '');
            }
        }
    }

    private function fixClosingBraceSpaces(int $position, int $numberOfSpaces) : void
    {
        if ($numberOfSpaces < $this->emptyLinesBeforeClosingBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesBeforeClosingBrace; $i++) {
                $this->file->fixer->addContentBefore($position, PHP_EOL);
            }
        } elseif ($numberOfSpaces > $this->emptyLinesBeforeClosingBrace) {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesBeforeClosingBrace; $i--) {
                $this->file->fixer->replaceToken($position - $i, '');
            }
        }
    }
}
