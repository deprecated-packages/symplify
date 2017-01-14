<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Commenting;

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
    public const NAME = 'SymplifyCodingStandard.Commenting.VarPropertyComment';

    /**
     * @param File $file
     * @param int $position
     */
    protected function processMemberVar(File $file, $position) : void
    {
        $commentString = $this->getPropertyComment($file, $position);

        if (strpos($commentString, '@var') !== false) {
            return;
        }

        $file->addError('Property should have docblock comment.', $position);
    }

    /**
     * @param File $file
     * @param int $position
     */
    protected function processVariable(File $file, $position)
    {
    }

    /**
     * @param File $file
     * @param int $position
     */
    protected function processVariableInString(File $file, $position) : void
    {
    }

    private function getPropertyComment(File $file, int $position) : string
    {
        $commentEnd = $file->findPrevious([T_DOC_COMMENT_CLOSE_TAG], $position);
        if ($commentEnd === false) {
            return '';
        }

        $tokens = $file->getTokens();
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return '';
        }

        // Make sure the comment we have found belongs to us.
        $commentFor = $file->findNext(T_VARIABLE, $commentEnd + 1);
        if ($commentFor !== $position) {
            return '';
        }

        $commentStart = $file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $position);
        return $file->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
    }
}
