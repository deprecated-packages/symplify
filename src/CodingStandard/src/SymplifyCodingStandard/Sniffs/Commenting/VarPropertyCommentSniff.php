<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace SymplifyCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Standards_AbstractVariableSniff;

/**
 * Rules:
 * - Property should have docblock comment.
 *
 * @see PHP_CodeSniffer_Standards_AbstractVariableSniff is used, because it's very difficult to
 * separate properties from variables (in args, method etc.). This class does is for us.
 */
final class VarPropertyCommentSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{
    protected function processMemberVar(PHP_CodeSniffer_File $file, $position)
    {
        $commentString = $this->getPropertyComment($file, $position);

        if (strpos($commentString, '@var') !== false) {
            return;
        }

        $file->addError('Property should have docblock comment and @var type.', $position);
    }

    protected function processVariable(PHP_CodeSniffer_File $file, $position)
    {
    }

    protected function processVariableInString(PHP_CodeSniffer_File $file, $position)
    {
    }

    private function getPropertyComment(PHP_CodeSniffer_File $file, int $position) : string
    {
        $commentEnd = $file->findPrevious([T_DOC_COMMENT_CLOSE_TAG], $position);
        if ($commentEnd === false) {
            return '';
        }

        $tokens = $file->getTokens();
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return '';
        } else {
            // Make sure the comment we have found belongs to us.
            $commentFor = $file->findNext(T_VARIABLE, $commentEnd + 1);
            if ($commentFor !== $position) {
                return '';
            }
        }

        $commentStart = $file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $position);

        return $file->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
    }
}
