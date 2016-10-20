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
 * Rules:
 * - Getters should have return type (except for {@inheritdoc}).
 */
final class MethodReturnTypeSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var string[]
     */
    private $getterMethodPrefixes = ['get', 'is', 'has', 'will', 'should'];

    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return [T_FUNCTION];
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        $this->file = $file;
        $this->position = $position;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $file->addError('Getters should have @return tag (except {@inheritdoc}).', $position);
    }

    private function shouldBeSkipped() : bool
    {
        if ($this->guessIsGetterMethod() === false) {
            return true;
        }

        if ($this->hasPhp7ReturnType()) {
            return true;
        }

        if ($this->hasMethodCommentReturnOrInheritDoc()) {
            return true;
        }

        return false;
    }

    private function guessIsGetterMethod() : bool
    {
        $methodName = $this->file->getDeclarationName($this->position);

        if ($this->isRawGetterName($methodName)) {
            return true;
        }

        if ($this->hasGetterNamePrefix($methodName)) {
            return true;
        }

        return false;
    }

    private function getMethodComment() : string
    {
        if (!$this->hasMethodComment()) {
            return '';
        }

        $commentStart = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->position - 1);
        $commentEnd = $this->file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $this->position - 1);

        return $this->file->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
    }

    private function hasMethodCommentReturnOrInheritDoc() : bool
    {
        $comment = $this->getMethodComment();

        if (strpos($comment, '{@inheritdoc}') !== false) {
            return true;
        }

        if (strpos($comment, '@return') !== false) {
            return true;
        }

        return false;
    }

    private function hasMethodComment() : bool
    {
        $tokens = $this->file->getTokens();
        $currentToken = $tokens[$this->position];
        $docBlockClosePosition = $this->file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $this->position);

        if ($docBlockClosePosition === false) {
            return false;
        }

        $docBlockCloseToken = $tokens[$docBlockClosePosition];
        if ($docBlockCloseToken['line'] === ($currentToken['line'] - 1)) {
            return true;
        }

        return false;
    }

    private function hasPhp7ReturnType() : bool
    {
        $tokens = $this->file->getTokens();
        $colonPosition = $this->file->findNext(T_COLON, $this->position, null, false);

        if ($tokens[$colonPosition]['code'] !== T_COLON) {
            return false;
        }

        if ($tokens[$this->position]['line'] === $tokens[$colonPosition]['line']) {
            return true;
        }

        return false;
    }

    private function isRawGetterName(string $methodName) : bool
    {
        return in_array($methodName, $this->getterMethodPrefixes);
    }

    private function hasGetterNamePrefix(string $methodName) : bool
    {
        foreach ($this->getterMethodPrefixes as $getterMethodPrefix) {
            if (strpos($methodName, $getterMethodPrefix) === 0) {
                $endPosition = strlen($getterMethodPrefix);
                $firstLetterAfterGetterPrefix = $methodName[$endPosition];

                if (ctype_upper($firstLetterAfterGetterPrefix)) {
                    return true;
                }
            }
        }

        return false;
    }
}
