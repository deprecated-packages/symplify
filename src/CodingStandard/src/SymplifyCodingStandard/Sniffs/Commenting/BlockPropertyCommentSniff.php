<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace SymplifyCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Rules:.
 *
 * - Block comment should be used instead of one liner.
 */
final class BlockPropertyCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;

    /**
     * @var array
     */
    private $tokens;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();

        $closeTagPosition = $file->findNext(T_DOC_COMMENT_CLOSE_TAG, $position + 1);
        if ($this->isPropertyOrMethodComment($closeTagPosition) === false) {
            return;
        } elseif ($this->isSingleLineDoc($position, $closeTagPosition) === false) {
            return;
        }

        $error = 'Block comment should be used instead of one liner';
        $file->addError($error, $position);
    }

    private function isPropertyOrMethodComment(int $position) : bool
    {
        $nextPropertyOrMethodPosition = $this->file->findNext([T_VARIABLE, T_FUNCTION], $position + 1);
        if (!$nextPropertyOrMethodPosition) {
            return false;
        }

        if ($this->isVariableOrPropertyUse($nextPropertyOrMethodPosition) === true) {
            return false;
        }

        return true;
    }

    private function isSingleLineDoc(int $openTagPosition, int $closeTagPosition) : bool
    {
        $lines = $this->tokens[$closeTagPosition]['line'] - $this->tokens[$openTagPosition]['line'];
        if ($lines < 2) {
            return true;
        }

        return false;
    }

    private function isVariableOrPropertyUse(int $position) : bool
    {
        if ($previous = $this->file->findPrevious(T_OPEN_CURLY_BRACKET, $position - 1)) {
            $previous = $this->file->findPrevious(T_OPEN_CURLY_BRACKET, $previous - 1);
            if ($this->tokens[$previous]['code'] === T_OPEN_CURLY_BRACKET) { // used in method
                return true;
            }
        }

        return false;
    }
}
