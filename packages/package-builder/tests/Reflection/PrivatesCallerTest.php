<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Reflection;

use Iterator;
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
        $this->privatesCaller = new PrivatesCaller();
    }

    /**
     * @dataProvider provideData()
     */
    public function test($object, string $methodName, array $arguments, int $expectedResult): void
    {
        $result = $this->privatesCaller->callPrivateMethod($object, $methodName, $arguments);
        $this->assertSame($expectedResult, $result);
    }

    public function provideData(): Iterator
    {
        yield [SomeClassWithPrivateMethods::class, 'getNumber', [], 5];
        yield [new SomeClassWithPrivateMethods(), 'getNumber', [], 5];
        yield [new SomeClassWithPrivateMethods(), 'plus10', [30], 40];
    }

    /**
     * @dataProvider provideDataReference()
     */
    public function testReference($object, string $methodName, $referencedArgument, int $expectedResult): void
    {
        $result = $this->privatesCaller->callPrivateMethodWithReference($object, $methodName, $referencedArgument);
        $this->assertSame($expectedResult, $result);
    }

    public function provideDataReference(): Iterator
    {
        yield [new SomeClassWithPrivateMethods(), 'multipleByTwo', 10, 20];
    }
}
