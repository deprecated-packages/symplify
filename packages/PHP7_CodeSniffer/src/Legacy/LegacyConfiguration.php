<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Legacy;

final class LegacyConfiguration
{
    public static function setup()
    {
        self::ensureLineEndingsAreDetected();
        self::setupVerbosityToMakeLegacyCodeRun();
    }

    /**
     * Ensure this option is enabled or else line endings will not always
     * be detected properly for files created on a Mac with the /r line ending.
     */
    private static function ensureLineEndingsAreDetected()
    {
        ini_set('auto_detect_line_endings', true);
    }

    private static function setupVerbosityToMakeLegacyCodeRun()
    {
        if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
    }
}
