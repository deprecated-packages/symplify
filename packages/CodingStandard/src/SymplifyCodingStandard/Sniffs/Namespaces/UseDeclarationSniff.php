<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff as Psr2UseDeclarationSniff;

/**
 * Rules:
 * - There must be one USE keyword per declaration
 * - USE declarations must go after the first namespace declaration
 * - There must be 2 blank line(s) after the last USE statement
 */
final class UseDeclarationSniff extends Psr2UseDeclarationSniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Namespaces.UseDeclaration';

    /**
     * @var int|string
     */
    public $blankLinesAfterUseStatement = 1;

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
    public function register() : array
    {
        return [T_USE];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        // Fix types
        $this->blankLinesAfterUseStatement = (int) $this->blankLinesAfterUseStatement;

        if ($this->shouldIgnoreUse($position) === true) {
            return;
        }

        $this->checkIfSingleSpaceAfterUseKeyword();
        $this->checkIfOneUseDeclarationPerStatement();
        $this->checkIfUseComesAfterNamespaceDeclaration();

        // Only interested in the last USE statement from here onwards.
        $nextUse = $file->findNext(T_USE, ($position + 1));

        if ($nextUse) {
            while ($this->shouldIgnoreUse($nextUse) === true) {
                $nextUse = $file->findNext(T_USE, ($nextUse + 1));
                if ($nextUse === false) {
                    break;
                }
            }
        }

        if ($nextUse !== false) {
            return;
        }

        $this->checkBlankLineAfterLastUseStatement();
    }

    /**
     * Check if this use statement is part of the namespace block.
     */
    private function shouldIgnoreUse(int $position) : bool
    {
        // Ignore USE keywords inside closures.
        $next = $this->file->findNext(T_WHITESPACE, ($position + 1), null, true);
        if ($this->tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return true;
        }

        // Ignore USE keywords for traits.
        if ($this->file->hasCondition($position, [T_CLASS, T_TRAIT]) === true) {
            return true;
        }

        return false;
    }

    private function checkIfSingleSpaceAfterUseKeyword() : void
    {
        if ($this->tokens[($this->position + 1)]['content'] !== ' ') {
            $this->file->addError('There must be a single space after the USE keyword', $this->position);
        }
    }

    private function checkIfOneUseDeclarationPerStatement() : void
    {
        $next = $this->file->findNext([T_COMMA, T_SEMICOLON], ($this->position + 1));
        if ($this->tokens[$next]['code'] === T_COMMA) {
            $this->file->addError('There must be one USE keyword per declaration', $this->position);
        }
    }

    private function checkIfUseComesAfterNamespaceDeclaration() : void
    {
        $prev = $this->file->findPrevious(T_NAMESPACE, ($this->position - 1));
        if ($prev !== false) {
            $first = $this->file->findNext(T_NAMESPACE, 1);
            if ($prev !== $first) {
                $this->file->addError(
                    'USE declarations must go after the first namespace declaration',
                    $this->position
                );
            }
        }
    }

    private function checkBlankLineAfterLastUseStatement() : void
    {
        $end = $this->file->findNext(T_SEMICOLON, ($this->position + 1));
        $next = $this->file->findNext(T_WHITESPACE, ($end + 1), null, true);
        $diff = ($this->tokens[$next]['line'] - $this->tokens[$end]['line'] - 1);
        if ($diff !== (int) $this->blankLinesAfterUseStatement) {
            if ($diff < 0) {
                $diff = 0;
            }

            $errorMessage = sprintf(
                'There must be %s blank line(s) after the last USE statement; %s found.',
                $this->blankLinesAfterUseStatement,
                $diff
            );
            $this->file->addError($errorMessage, $this->position);
        }
    }
}
