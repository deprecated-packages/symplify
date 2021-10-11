<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

/**
 * @api
 */
final class StaticEolConfiguration
{
    public static function getEolChar(): string
    {
        return "\n";
    }
}
