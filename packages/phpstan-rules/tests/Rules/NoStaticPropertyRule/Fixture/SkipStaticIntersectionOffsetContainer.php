<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

use PHPStan\DependencyInjection\Container;

final class SkipStaticIntersectionOffsetContainer
{
    /**
     * @var array<string, Container>
     */
    private static $containersByConfig = [];

    private function getServiceContainer(string $config): Container
    {
        if (isset(self::$containersByConfig[$config])) {
            return self::$containersByConfig[$config];
        }

        self::$containersByConfig[$config] = $container;
    }
}
