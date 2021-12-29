<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Reflection;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Tests\Reflection\Source\SomeClassWithPrivateProperty;

final class PrivatesAccessorTest extends TestCase
{
    public function testGetter(): void
    {
        $privatesAccessor = new PrivatesAccessor();
        $someClassWithPrivateProperty = new SomeClassWithPrivateProperty();

        $fetchedValue = $privatesAccessor->getPrivateProperty($someClassWithPrivateProperty, 'value');
        $this->assertSame($someClassWithPrivateProperty->getValue(), $fetchedValue);

        $fetchedParentValue = $privatesAccessor->getPrivateProperty($someClassWithPrivateProperty, 'parentValue');
        $this->assertSame($someClassWithPrivateProperty->getParentValue(), $fetchedParentValue);

        $fetchedValue = $privatesAccessor->getPrivatePropertyOfClass($someClassWithPrivateProperty, 'object', stdClass::class);
        $this->assertSame($someClassWithPrivateProperty->getObject(), $fetchedValue);
    }

    public function testSetter(): void
    {
        $privatesAccessor = new PrivatesAccessor();
        $someClassWithPrivateProperty = new SomeClassWithPrivateProperty();

        $privatesAccessor->setPrivateProperty($someClassWithPrivateProperty, 'value', 25);
        $this->assertSame(25, $someClassWithPrivateProperty->getValue());

        $newObject = new stdClass();
        $this->assertNotSame($newObject, $someClassWithPrivateProperty->getObject());
        $privatesAccessor->setPrivatePropertyOfClass($someClassWithPrivateProperty, 'object', $newObject, stdClass::class);
        $this->assertSame($newObject, $someClassWithPrivateProperty->getObject());
    }
}
