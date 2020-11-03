<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticPropertyRule\Fixture;

use Symfony\Component\HttpKernel\KernelInterface;

final class SkipStaticKernel
{
    /**
     * @var KernelInterface
     */
    public static $kernel = [];

    public static function getKernel(): KernelInterface
    {
        return self::$kernel;
    }
}
