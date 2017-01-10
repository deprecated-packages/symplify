<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Tokens;
use Squiz_Sniffs_ControlStructures_SwitchDeclarationSniff;

final class SwitchDeclarationSniff extends Squiz_Sniffs_ControlStructures_SwitchDeclarationSniff
{
    /**
     * @var string
     */
    const NAME = 'SymplifyCodingStandard.ControlStructures.SwitchDeclaration';

    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    public $indent = 1;

    /**
     * @var array
     */
    private $token;

    /**
     * @var array[]
     */
    private $tokens;

    /**
     * @var int
     */
    private $position;

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
        $this->file = $file;
        $this->position = $position;

        $this->tokens = $tokens = $file->getTokens();
        $this->token = $tokens[$position];

        if ($this->areSwitchStartAndEndKnown() === false) {
            return;
        }

        $switch = $tokens[$position];
        $nextCase = $position;
        $caseAlignment = ($switch['column'] + $this->indent);
        $caseCount = 0;
        $foundDefault = false;

        $lookFor = [T_CASE, T_DEFAULT, T_SWITCH];
        while (($nextCase = $file->findNext($lookFor, ($nextCase + 1), $switch['scope_closer'])) !== false) {
            // Skip nested SWITCH statements; they are handled on their own.
            if ($tokens[$nextCase]['code'] === T_SWITCH) {
                $nextCase = $tokens[$nextCase]['scope_closer'];
                continue;
            }
            if ($tokens[$nextCase]['code'] === T_DEFAULT) {
                $type = 'Default';
                $foundDefault = true;
            } else {
                $type = 'Case';
                $caseCount++;
            }

            $this->checkIfKeywordIsIndented($file, $nextCase, $tokens, $type, $caseAlignment);
            $this->checkSpaceAfterKeyword($nextCase, $type);

            $opener = $tokens[$nextCase]['scope_opener'];

            $this->ensureNoSpaceBeforeColon($opener, $nextCase, $type);

            $nextBreak = $tokens[$nextCase]['scope_closer'];

            $allowedTokens = [T_BREAK, T_RETURN, T_CONTINUE, T_THROW, T_EXIT];
            if (in_array($tokens[$nextBreak]['code'], $allowedTokens)) {
                $this->processSwitchStructureToken($nextBreak, $nextCase, $caseAlignment, $type, $opener);
            } elseif ($type === 'Default') {
                $error = 'DEFAULT case must have a breaking statement';
                $file->addError($error, $nextCase, 'DefaultNoBreak');
            }
        }

        $this->ensureDefaultIsPresent($foundDefault);
        $this->ensureClosingBraceAlignment($switch);
    }

    private function checkIfKeywordIsIndented(
        PHP_CodeSniffer_File $file,
        int $position,
        array $tokens,
        string $type,
        int $caseAlignment
    ) {
        if ($tokens[$position]['column'] !== $caseAlignment) {
            $error = strtoupper($type) . ' keyword must be indented ' . $this->indent . ' spaces from SWITCH keyword';
            $file->addError($error, $position, $type . 'Indent');
        }
    }

    private function checkBreak(int $nextCase, int $nextBreak, string $type)
    {
        if ($type === 'Case') {
            // Ensure empty CASE statements are not allowed.
            // They must have some code content in them. A comment is not enough.
            // But count RETURN statements as valid content if they also
            // happen to close the CASE statement.
            $foundContent = false;
            for ($i = ($this->tokens[$nextCase]['scope_opener'] + 1); $i < $nextBreak; $i++) {
                if ($this->tokens[$i]['code'] === T_CASE) {
                    $i = $this->tokens[$i]['scope_opener'];
                    continue;
                }

                $tokenCode = $this->tokens[$i]['code'];
                $emptyTokens = PHP_CodeSniffer_Tokens::$emptyTokens;
                if (in_array($tokenCode, $emptyTokens) === false) {
                    $foundContent = true;
                    break;
                }
            }
            if ($foundContent === false) {
                $error = 'Empty CASE statements are not allowed';
                $this->file->addError($error, $nextCase, 'EmptyCase');
            }
        } else {
            // Ensure empty DEFAULT statements are not allowed.
            // They must (at least) have a comment describing why
            // the default case is being ignored.
            $foundContent = false;
            for ($i = ($this->tokens[$nextCase]['scope_opener'] + 1); $i < $nextBreak; $i++) {
                if ($this->tokens[$i]['type'] !== 'T_WHITESPACE') {
                    $foundContent = true;
                    break;
                }
            }
            if ($foundContent === false) {
                $error = 'Comment required for empty DEFAULT case';
                $this->file->addError($error, $nextCase, 'EmptyDefault');
            }
        }
    }

    private function areSwitchStartAndEndKnown() : bool
    {
        if (! isset($this->tokens[$this->position]['scope_opener'])) {
            return false;
        }

        if (! isset($this->tokens[$this->position]['scope_closer'])) {
            return false;
        }

        return true;
    }

    private function processSwitchStructureToken(
        int $nextBreak,
        int $nextCase,
        int $caseAlignment,
        string $type,
        int $opener
    ) {
        if ($this->tokens[$nextBreak]['scope_condition'] === $nextCase) {
            $this->ensureCaseIndention($nextBreak, $caseAlignment);

            $this->ensureNoBlankLinesBeforeBreak($nextBreak);

            $breakLine = $this->tokens[$nextBreak]['line'];
            $nextLine = $this->getNextLineFromNextBreak($nextBreak);
            if ($type !== 'Case') {
                $this->ensureBreakIsNotFollowedByBlankLine($nextLine, $breakLine, $nextBreak);
            }

            $this->ensureNoBlankLinesAfterStatement($nextCase, $nextBreak, $type, $opener);
        }

        if ($this->tokens[$nextBreak]['code'] === T_BREAK) {
            $this->checkBreak($nextCase, $nextBreak, $type);
        }
    }

    private function ensureBreakIsNotFollowedByBlankLine(int $nextLine, int $breakLine, int $nextBreak)
    {
        if ($nextLine !== ($breakLine + 1)) {
            $error = 'Blank lines are not allowed after the DEFAULT case\'s breaking statement';
            $this->file->addError($error, $nextBreak, 'SpacingAfterDefaultBreak');
        }
    }

    private function ensureNoBlankLinesBeforeBreak(int $nextBreak)
    {
        $prev = $this->file->findPrevious(T_WHITESPACE, ($nextBreak - 1), $this->position, true);
        if ($this->tokens[$prev]['line'] !== ($this->tokens[$nextBreak]['line'] - 1)) {
            $error = 'Blank lines are not allowed before case breaking statements';
            $this->file->addError($error, $nextBreak, 'SpacingBeforeBreak');
        }
    }

    private function ensureNoBlankLinesAfterStatement(int $nextCase, int $nextBreak, string $type, int $opener)
    {
        $caseLine = $this->tokens[$nextCase]['line'];
        $nextLine = $this->tokens[$nextBreak]['line'];
        for ($i = ($opener + 1); $i < $nextBreak; $i++) {
            if ($this->tokens[$i]['type'] !== 'T_WHITESPACE') {
                $nextLine = $this->tokens[$i]['line'];
                break;
            }
        }
        if ($nextLine !== ($caseLine + 1)) {
            $error = 'Blank lines are not allowed after ' . strtoupper($type) . ' statements';
            $this->file->addError($error, $nextCase, 'SpacingAfter' . $type);
        }
    }

    private function getNextLineFromNextBreak(int $nextBreak) : int
    {
        $semicolon = $this->file->findNext(T_SEMICOLON, $nextBreak);
        for ($i = ($semicolon + 1); $i < $this->tokens[$this->position]['scope_closer']; $i++) {
            if ($this->tokens[$i]['type'] !== 'T_WHITESPACE') {
                return $this->tokens[$i]['line'];
            }
        }

        return $this->tokens[$this->tokens[$this->position]['scope_closer']]['line'];
    }

    private function ensureCaseIndention(int $nextBreak, int $caseAlignment)
    {
        // Only need to check a couple of things once, even if the
        // break is shared between multiple case statements, or even
        // the default case.
        if (($this->tokens[$nextBreak]['column'] - 1) !== $caseAlignment) {
            $error = 'Case breaking statement must be indented ' . ($this->indent + 1) . ' tabs from SWITCH keyword';
            $this->file->addError($error, $nextBreak, 'BreakIndent');
        }
    }

    private function ensureDefaultIsPresent(bool $foundDefault)
    {
        if ($foundDefault === false) {
            $error = 'All SWITCH statements must contain a DEFAULT case';
            $this->file->addError($error, $this->position, 'MissingDefault');
        }
    }

    private function ensureClosingBraceAlignment(array $switch)
    {
        if ($this->tokens[$switch['scope_closer']]['column'] !== $switch['column']) {
            $error = 'Closing brace of SWITCH statement must be aligned with SWITCH keyword';
            $this->file->addError($error, $switch['scope_closer'], 'CloseBraceAlign');
        }
    }

    private function ensureNoSpaceBeforeColon(int $opener, int $nextCase, string $type)
    {
        if ($this->tokens[($opener - 1)]['type'] === 'T_WHITESPACE') {
            $error = 'There must be no space before the colon in a ' . strtoupper($type) . ' statement';
            $this->file->addError($error, $nextCase, 'SpaceBeforeColon' . $type);
        }
    }

    private function checkSpaceAfterKeyword(int $nextCase, string $type)
    {
        if ($type === 'Case' && ($this->tokens[($nextCase + 1)]['type'] !== 'T_WHITESPACE'
            || $this->tokens[($nextCase + 1)]['content'] !== ' ')
        ) {
            $error = 'CASE keyword must be followed by a single space';
            $this->file->addError($error, $nextCase, 'SpacingAfterCase');
        }
    }
}
