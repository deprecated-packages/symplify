<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Composer;

use Composer\Autoload\ClassLoader;
use ReflectionClass;

final class VendorDirProvider
{
    /**
     * @var string
     */
    private static $vendorDir;

    public static function provide() : string
    {
        if (self::$vendorDir) {
            return self::$vendorDir;
        }

        $classLoaderReflection = new ReflectionClass(ClassLoader::class);
        return self::$vendorDir = dirname(dirname($classLoaderReflection->getFileName()));
    }
}
