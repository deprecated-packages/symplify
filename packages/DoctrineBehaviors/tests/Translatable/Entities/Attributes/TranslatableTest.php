<?php

declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes;

use PHPUnit\Framework\TestCase;
use Zenify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes\Source\TranslatableEntity;
use Zenify\DoctrineBehaviors\Tests\Translatable\Entities\Attributes\Source\TranslatableEntityWithNetteObject;

final class TranslatableTest extends TestCase
{

    public function testGetterMethod()
    {
        $translatableEntity = new TranslatableEntity;
        $this->assertSame('someName', $translatableEntity->getName());
    }


    public function testProperty()
    {
        $translatableEntity = new TranslatableEntity;
        $this->assertSame(5, $translatableEntity->position);
    }


    public function testGetterMethodWithNetteObject()
    {
        $translatableEntity = new TranslatableEntityWithNetteObject;
        $this->assertSame('someName', $translatableEntity->getName());
    }


    public function testPropertyWithNetteObject()
    {
        $translatableEntity = new TranslatableEntityWithNetteObject;
        $this->assertSame(5, $translatableEntity->position);
    }
}
