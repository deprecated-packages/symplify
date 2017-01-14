<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\FunctionSpacingSniff;
use Symplify\CodingStandard\Helper\Whitespace\EmptyLinesResizer;

/**
 * Rules:
 * - Method should have X empty line(s) after itself.
 *
 * Exceptions:
 * - Method is the first in the class, preceded by open bracket.
 * - Method is the last in the class, followed by close bracket.
 */
final class InBetweenMethodSpacingSniff extends FunctionSpacingSniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.WhiteSpace.InBetweenMethodSpacing';

    /**
     * @var int|string
     */
    public $blankLinesBetweenMethods = 1;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $tokens;

    /**
     * @var File
     */
    private $file;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_FUNCTION];
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

        // Fix type
        $this->blankLinesBetweenMethods = (int) $this->blankLinesBetweenMethods;

        $blankLinesCountAfterFunction = $this->getBlankLineCountAfterFunction();
        if ($blankLinesCountAfterFunction !== $this->blankLinesBetweenMethods) {
            if ($this->isLastMethod()) {
                return;
            }

            $error = sprintf(
                'Method should have %s empty line(s) after itself, %s found.',
                $this->blankLinesBetweenMethods,
                $blankLinesCountAfterFunction
            );
            $fix = $file->addFixableError($error, $position);
            if ($fix) {
                $this->fixSpacingAfterMethod($blankLinesCountAfterFunction);
            }
        }
    }

    private function getBlankLineCountAfterFunction() : int
    {
        $closer = $this->getScopeCloser();
        $nextLineToken = $this->getNextLineTokenByScopeCloser($closer);

        $nextContent = $this->getNextLineContent($nextLineToken);
        if ($nextContent !== false) {
            $foundLines = ($this->tokens[$nextContent]['line'] - $this->tokens[$nextLineToken]['line']);
        } else {
            // We are at the end of the file.
            $foundLines = $this->blankLinesBetweenMethods;
        }

        return $foundLines;
    }

    private function isLastMethod() : bool
    {
        $closer = $this->getScopeCloser();
        $nextLineToken = $this->getNextLineTokenByScopeCloser($closer);
        if ($this->tokens[$nextLineToken + 1]['code'] === T_CLOSE_CURLY_BRACKET) {
            return true;
        }
        return false;
    }

    /**
     * @return bool|int
     */
    private function getScopeCloser()
    {
        if (isset($this->tokens[$this->position]['scope_closer']) === false) {
            // Must be an interface method, so the closer is the semi-colon.
            return $this->file->findNext(T_SEMICOLON, $this->position);
        }

        return $this->tokens[$this->position]['scope_closer'];
    }

    /**
     * @return int|NULL
     */
    private function getNextLineTokenByScopeCloser(int $closer)
    {
        $nextLineToken = null;
        for ($i = $closer; $i < $this->file->numTokens; $i++) {
            if (strpos($this->tokens[$i]['content'], $this->file->eolChar) === false) {
                continue;
            }

            $nextLineToken = ($i + 1);
            if (! isset($this->tokens[$nextLineToken])) {
                $nextLineToken = null;
            }

            break;
        }
        return $nextLineToken;
    }

    /**
     * @return false|int
     */
    private function getNextLineContent(int $nextLineToken)
    {
        if ($nextLineToken !== null) {
            return $this->file->findNext(T_WHITESPACE, ($nextLineToken + 1), null, true);
        }
        return false;
    }

    private function fixSpacingAfterMethod(int $blankLinesCountAfterFunction)
    {
        EmptyLinesResizer::resizeLines(
            $this->file,
            $this->getScopeCloser() + 1,
            $blankLinesCountAfterFunction,
            $this->blankLinesBetweenMethods
        );
    }
}
