<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticPropertyRule\Fixture;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class SkipNullableContainerCache extends TestCase
{
    /**
     * @var ContainerInterface|null
     */
    public static $container = [];

    public static function getContainer(): ?ContainerInterface
    {
        return self::$container;
    }
}
