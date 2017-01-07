<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Legacy;

final class LegacyCompatibilityLayer
{
    /**
     * @var bool
     */
    private static $isAdded = false;

    public static function add()
    {
        if (self::$isAdded) {
            return;
        }

        LegacyConfiguration::setup();
        LegacyClassAliases::register();

        self::$isAdded = true;
    }
}
