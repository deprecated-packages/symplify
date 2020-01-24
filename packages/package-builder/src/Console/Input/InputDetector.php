<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;

final class InputDetector
{
    public static function isDebug(): bool
    {
        return (new ArgvInput())->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}
