<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use PSR2_Sniffs_Namespaces_UseDeclarationSniff;

/**
 * Rules:
 * - There must be one USE keyword per declaration
 * - USE declarations must go after the first namespace declaration
 * - There must be 2 blank line(s) after the last USE statement
 */
final class UseDeclarationSniff extends PSR2_Sniffs_Namespaces_UseDeclarationSniff
{
    /**
     * @var string
     */
    const NAME = 'SymplifyCodingStandard.Namespaces.UseDeclaration';

    /**
     * @var int|string
     */
    public $blankLinesAfterUseStatement = 1;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_USE];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $position
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        // Fix types
        $this->blankLinesAfterUseStatement = (int) $this->blankLinesAfterUseStatement;

        if ($this->shouldIgnoreUse($file, $position) === true) {
            return;
        }

        $this->checkIfSingleSpaceAfterUseKeyword($file, $position);
        $this->checkIfOneUseDeclarationPerStatement($file, $position);
        $this->checkIfUseComesAfterNamespaceDeclaration($file, $position);

        // Only interested in the last USE statement from here onwards.
        $nextUse = $file->findNext(T_USE, ($position + 1));
        while ($this->shouldIgnoreUse($file, $nextUse) === true) {
            $nextUse = $file->findNext(T_USE, ($nextUse + 1));
            if ($nextUse === false) {
                break;
            }
        }

        if ($nextUse !== false) {
            return;
        }

        $this->checkBlankLineAfterLastUseStatement($file, $position);
    }

    /**
     * Check if this use statement is part of the namespace block.
     * @param PHP_CodeSniffer_File $file
     * @param int|bool $position
     */
    private function shouldIgnoreUse(PHP_CodeSniffer_File $file, $position) : bool
    {
        $tokens = $file->getTokens();

        // Ignore USE keywords inside closures.
        $next = $file->findNext(T_WHITESPACE, ($position + 1), null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return true;
        }

        // Ignore USE keywords for traits.
        if ($file->hasCondition($position, [T_CLASS, T_TRAIT]) === true) {
            return true;
        }

        return false;
    }

    private function checkIfSingleSpaceAfterUseKeyword(PHP_CodeSniffer_File $file, int $position)
    {
        $tokens = $file->getTokens();
        if ($tokens[($position + 1)]['content'] !== ' ') {
            $error = 'There must be a single space after the USE keyword';
            $file->addError($error, $position, 'SpaceAfterUse');
        }
    }

    private function checkIfOneUseDeclarationPerStatement(PHP_CodeSniffer_File $file, int $position)
    {
        $tokens = $file->getTokens();
        $next = $file->findNext([T_COMMA, T_SEMICOLON], ($position + 1));
        if ($tokens[$next]['code'] === T_COMMA) {
            $error = 'There must be one USE keyword per declaration';
            $file->addError($error, $position, 'MultipleDeclarations');
        }
    }

    private function checkIfUseComesAfterNamespaceDeclaration(PHP_CodeSniffer_File $file, int $position)
    {
        $prev = $file->findPrevious(T_NAMESPACE, ($position - 1));
        if ($prev !== false) {
            $first = $file->findNext(T_NAMESPACE, 1);
            if ($prev !== $first) {
                $error = 'USE declarations must go after the first namespace declaration';
                $file->addError($error, $position, 'UseAfterNamespace');
            }
        }
    }

    private function checkBlankLineAfterLastUseStatement(PHP_CodeSniffer_File $file, int $position)
    {
        $tokens = $file->getTokens();
        $end = $file->findNext(T_SEMICOLON, ($position + 1));
        $next = $file->findNext(T_WHITESPACE, ($end + 1), null, true);
        $diff = ($tokens[$next]['line'] - $tokens[$end]['line'] - 1);
        if ($diff !== (int) $this->blankLinesAfterUseStatement) {
            if ($diff < 0) {
                $diff = 0;
            }

            $error = 'There must be %s blank line(s) after the last USE statement; %s found.';
            $data = [$this->blankLinesAfterUseStatement, $diff];
            $file->addError($error, $position, 'SpaceAfterLastUse', $data);
        }
    }
}
