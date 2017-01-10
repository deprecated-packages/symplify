<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Rules:
 * - Not operator (!) should be surrounded by spaces.
 */
final class ExclamationMarkSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var string
     */
    const NAME = 'SymplifyCodingStandard.WhiteSpace.ExclamationMark';

    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;


    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_BOOLEAN_NOT];
    }


    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        $this->file = $file;

        $tokens = $file->getTokens();
        $hasSpaceBefore = $tokens[$position - 1]['code'] === T_WHITESPACE;
        $hasSpaceAfter = $tokens[$position + 1]['code'] === T_WHITESPACE;

        if (! $hasSpaceBefore || ! $hasSpaceAfter) {
            $error = 'Not operator (!) should be surrounded by spaces.';
            $fix = $file->addFixableError($error, $position);
            if ($fix) {
                $this->fixSpacesAroundExclamationMark($position, $hasSpaceBefore, $hasSpaceAfter);
            }
        }
    }


    private function fixSpacesAroundExclamationMark(int $position, bool $isSpaceBefore, bool $isSpaceAfter)
    {
        if (! $isSpaceBefore) {
            $this->file->fixer->addContentBefore($position, ' ');
        }

        if (! $isSpaceAfter) {
            $this->file->fixer->addContentBefore($position + 1, ' ');
        }
    }
}
