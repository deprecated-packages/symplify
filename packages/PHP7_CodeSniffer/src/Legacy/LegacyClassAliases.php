<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Legacy;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractPatternSniff;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use Symplify\PHP7_CodeSniffer\Composer\VendorDirProvider;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class LegacyClassAliases
{
    public static function register()
    {
        if (class_exists('PHP_CodeSniffer_File')) {
            return;
        }

        new Tokens();

        class_alias(File::class, 'PHP_CodeSniffer_File');
        class_alias(Sniff::class, 'PHP_CodeSniffer_Sniff');

        self::registerAbstractSniffAliases();
        self::registerSniffAliases();
    }

    private static function registerAbstractSniffAliases()
    {
        class_alias(AbstractVariableSniff::class, 'PHP_CodeSniffer_Standards_AbstractVariableSniff');
        class_alias(AbstractPatternSniff::class, 'PHP_CodeSniffer_Standards_AbstractPatternSniff');
        class_alias(AbstractScopeSniff::class, 'PHP_CodeSniffer_Standards_AbstractScopeSniff');
    }

    private static function registerSniffAliases()
    {
        $sniffFinder = new SniffFinder(new SniffClassRobotLoaderFactory(), new SniffClassFilter());

        $sniffClasses = $sniffFinder->findAllSniffClassesInDirectory(VendorDirProvider::provide() . '/squizlabs/php_codesniffer/src/Standards');
        foreach ($sniffClasses as $sniffCode => $sniffClass) {
            $legacySniffClass = self::convertSniffCodeToLegacyClassName($sniffCode);
            class_alias($sniffClass, $legacySniffClass);
        }
    }

    private static function convertSniffCodeToLegacyClassName(string $sniffCode) : string
    {
        $parts = explode('.', $sniffCode);
        return $parts[0] . '_Sniffs_' . $parts[1] . '_' . $parts[2] . 'Sniff';
    }
}
