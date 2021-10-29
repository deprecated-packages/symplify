<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source;

final class StaticFilter
{
    public static function process(string $var): string
    {
        return $var;
    }
}
