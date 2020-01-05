<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Reflection;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Tests\Reflection\Source\SomeClassWithPrivateMethods;

final class PrivatesCallerTest extends TestCase
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    protected function setUp(): void
    {
        $this->privatesCaller = (new PrivatesCaller());
    }

    public function testCallPrivateMethod(): void
    {
        $this->assertSame(5, $this->privatesCaller->callPrivateMethod(
            SomeClassWithPrivateMethods::class,
            'getNumber'
        ));

        $this->assertSame(5, $this->privatesCaller->callPrivateMethod(
            new SomeClassWithPrivateMethods(),
            'getNumber'
        ));

        $this->assertSame(40, $this->privatesCaller->callPrivateMethod(
            new SomeClassWithPrivateMethods(),
            'plus10',
            30
        ));
    }

    public function testCallPrivateMethodWithReference(): void
    {
        $this->assertSame(20, $this->privatesCaller->callPrivateMethodWithReference(
            new SomeClassWithPrivateMethods(),
            'multipleByTwo',
            10
        ));
    }
}
