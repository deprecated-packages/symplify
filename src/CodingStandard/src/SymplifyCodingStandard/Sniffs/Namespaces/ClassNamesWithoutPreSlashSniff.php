<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace SymplifyCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Rules:
 * - Class name after new/instanceof should not start with slash.
 */
final class ClassNamesWithoutPreSlashSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var string[]
     */
    private $excludedClassNames = [
        'DateTime', 'stdClass', 'splFileInfo', 'Exception',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return [T_NEW, T_INSTANCEOF];
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $file, $position)
    {
        $tokens = $file->getTokens();
        $classNameStart = $tokens[$position + 2]['content'];

        if ($classNameStart === '\\') {
            if ($this->isExcludedClassName($tokens[$position + 3]['content'])) {
                return;
            }
            $file->addError('Class name after new/instanceof should not start with slash.', $position);
        }
    }

    private function isExcludedClassName(string $className) : bool
    {
        if (in_array($className, $this->excludedClassNames)) {
            return true;
        }

        return false;
    }
}
