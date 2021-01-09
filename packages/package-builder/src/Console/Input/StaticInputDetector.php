<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;

final class StaticInputDetector
{
    public static function isDebug(): bool
    {
        $argvInput = new ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
