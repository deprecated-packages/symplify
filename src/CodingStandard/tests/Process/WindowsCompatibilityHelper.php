<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Process;

final class WindowsCompatibilityHelper
{
    public static function makeWindowsOsCompatible(string $command) : string
    {
        if (!self::isWindows()) {
            return $command;
        }

        return str_replace(['"', "'"], ["'", '"'], $command);
    }

    private static function isWindows() : bool
    {
        return '\\' === DIRECTORY_SEPARATOR;
    }
}
