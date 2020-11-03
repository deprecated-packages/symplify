<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class SkipContainerArrayCache extends TestCase
{
    /**
     * @var ContainerInterface[]
     */
    public static $containers = [];

    /**
     * @var array<string, ContainerInterface>
     */
    public static $arrayContainers = [];

    public function getContainers(): array
    {
        return self::$containers;
    }

    public function getArrayContainers(): array
    {
        return self::$arrayContainers;
    }
}
