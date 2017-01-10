<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Classes;

use PEAR_Sniffs_Classes_ClassDeclarationSniff;
use PHP_CodeSniffer_File;

/**
 * Rules (new to parent class):
 * - Opening brace for the %s should be followed by %s empty line(s).
 * - Closing brace for the %s should be preceded by %s empty line(s).
 */
final class ClassDeclarationSniff extends PEAR_Sniffs_Classes_ClassDeclarationSniff
{

    /**
     * @var string
     */
    const NAME = 'SymplifyCodingStandard.Classes.ClassDeclaration';

    /**
     * @var int|string
     */
    public $emptyLinesAfterOpeningBrace = 1;

    /**
     * @var int|string
     */
    public $emptyLinesBeforeClosingBrace = 1;

    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;


    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        parent::process($file, $position);
        $this->file = $file;

        // Fix type
        $this->emptyLinesAfterOpeningBrace = (int) $this->emptyLinesAfterOpeningBrace;
        $this->emptyLinesBeforeClosingBrace = (int) $this->emptyLinesBeforeClosingBrace;

        $this->processOpen($file, $position);
        $this->processClose($file, $position);
    }


    private function processOpen(PHP_CodeSniffer_File $file, int $position)
    {
        $tokens = $file->getTokens();
        $openingBracePosition = $tokens[$position]['scope_opener'];
        $emptyLinesCount = $this->getEmptyLinesAfterOpeningBrace($file, $openingBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesAfterOpeningBrace) {
            $error = 'Opening brace for the %s should be followed by %s empty line(s); %s found.';
            $data = [
                $tokens[$position]['content'],
                $this->emptyLinesAfterOpeningBrace,
                $emptyLinesCount,
            ];
            $fix = $file->addFixableError($error, $openingBracePosition, 'OpenBraceFollowedByEmptyLines', $data);
            if ($fix) {
                $this->fixOpeningBraceSpaces($openingBracePosition, $emptyLinesCount);
            }
        }
    }


    private function processClose(PHP_CodeSniffer_File $file, int $position)
    {
        $tokens = $file->getTokens();
        $closeBracePosition = $tokens[$position]['scope_closer'];
        $emptyLinesCount = $this->getEmptyLinesBeforeClosingBrace($file, $closeBracePosition);

        if ($emptyLinesCount !== $this->emptyLinesBeforeClosingBrace) {
            $error = 'Closing brace for the %s should be preceded by %s empty line(s); %s found.';
            $data = [
                $tokens[$position]['content'],
                $this->emptyLinesBeforeClosingBrace,
                $emptyLinesCount
            ];
            $fix = $file->addFixableError($error, $closeBracePosition, 'CloseBracePrecededByEmptyLines', $data);
            if ($fix) {
                $this->fixClosingBraceSpaces($closeBracePosition, $emptyLinesCount);
            }
        }
    }


    private function getEmptyLinesBeforeClosingBrace(PHP_CodeSniffer_File $file, int $position) : int
    {
        $tokens = $file->getTokens();
        $prevContent = $file->findPrevious(T_WHITESPACE, ($position - 1), null, true);
        return $tokens[$position]['line'] - $tokens[$prevContent]['line'] - 1;
    }


    private function getEmptyLinesAfterOpeningBrace(PHP_CodeSniffer_File $file, int $position) : int
    {
        $tokens = $file->getTokens();
        $nextContent = $file->findNext(T_WHITESPACE, ($position + 1), null, true);
        return $tokens[$nextContent]['line'] - $tokens[$position]['line'] - 1;
    }


    private function fixOpeningBraceSpaces(int $position, int $numberOfSpaces)
    {
        if ($numberOfSpaces < $this->emptyLinesAfterOpeningBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesAfterOpeningBrace; $i++) {
                $this->file->fixer->addContent($position, PHP_EOL);
            }
        } else {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesAfterOpeningBrace; $i--) {
                $this->file->fixer->replaceToken($position + $i, '');
            }
        }
    }


    private function fixClosingBraceSpaces(int $position, int $numberOfSpaces)
    {
        if ($numberOfSpaces < $this->emptyLinesBeforeClosingBrace) {
            for ($i = $numberOfSpaces; $i < $this->emptyLinesBeforeClosingBrace; $i++) {
                $this->file->fixer->addContentBefore($position, PHP_EOL);
            }
        } else {
            for ($i = $numberOfSpaces; $i > $this->emptyLinesBeforeClosingBrace; $i--) {
                $this->file->fixer->replaceToken($position - $i, '');
            }
        }
    }
}
