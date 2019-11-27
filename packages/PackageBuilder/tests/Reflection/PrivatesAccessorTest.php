<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Reflection;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Tests\Reflection\Source\SomeClassWithPrivateProperty;

final class PrivatesAccessorTest extends TestCase
{
    public function test(): void
    {
        $privatesAccessor = new PrivatesAccessor();
        $someClassWithPrivateProperty = new SomeClassWithPrivateProperty();

        $this->assertSame(
            $someClassWithPrivateProperty->getValue(),
            $privatesAccessor->getPrivateProperty($someClassWithPrivateProperty, 'value')
        );

        $this->assertSame(
            $someClassWithPrivateProperty->getParentValue(),
            $privatesAccessor->getPrivateProperty($someClassWithPrivateProperty, 'parentValue')
        );
    }
}
