<?php

declare(strict_types = 1);

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
