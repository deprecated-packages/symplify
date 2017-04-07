<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;

/**
 * Rules:
 * - Property should have docblock comment (except for {@inheritdoc}).
 *
 * @see PHP_CodeSniffer_Standards_AbstractVariableSniff is used, because it's very difficult to
 * separate properties from variables (in args, method etc.). This class does is for us.
 */
final class VarPropertyCommentSniff extends AbstractVariableSniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Property should have docblock comment.';

    /**
     * @var File
     */
    private $file;

    /**
     * @var mixed[]
     */
    private $tokens;

    /**
     * @var int
     */
    private $position;

    /**
     * @param File $file
     * @param int $position
     */
    protected function processMemberVar(File $file, $position): void
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->position = $position;
        $commentString = $this->getPropertyComment();

        if (strpos($commentString, '@var') !== false) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, self::class);
    }

    /**
     * @param File $file
     * @param int $position
     */
    protected function processVariable(File $file, $position): void
    {
    }

    /**
     * @param File $file
     * @param int $position
     */
    protected function processVariableInString(File $file, $position): void
    {
    }

    private function getPropertyComment(): string
    {
        $commentEnd = $this->file->findPrevious([T_DOC_COMMENT_CLOSE_TAG], $this->position);
        if ($commentEnd === false) {
            return '';
        }

        if ($this->tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return '';
        }

        if (! $this->doesCommentBelongToProperty($commentEnd)) {
            return '';
        }

        $commentStart = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->position);
        if (! is_int($commentStart)) {
            return '';
        }

        return $this->file->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
    }

    private function doesCommentBelongToProperty(int $commentEnd): bool
    {
        $commentFor = $this->file->findNext(T_VARIABLE, $commentEnd + 1);

        return $commentFor === $this->position;
    }
}
