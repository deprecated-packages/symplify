<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class SkipContainerCache extends TestCase
{
    /**
     * @var ContainerInterface
     */
    public static $container = [];

    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }
}
