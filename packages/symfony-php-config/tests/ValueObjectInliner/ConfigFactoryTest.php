<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner;

use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SymfonyPhpConfig\Tests\HttpKernel\SymfonyPhpConfigKernel;
use Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\Source\ServiceWithValueObject;
use Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\Source\WithType;

final class ConfigFactoryTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(SymfonyPhpConfigKernel::class, [
            __DIR__ . '/config/config_with_nested_value_objects.php',
        ]);
    }

    public function testInlineValueObjectFunction(): void
    {
        /** @var ServiceWithValueObject $serviceWithValueObject */
        $serviceWithValueObject = $this->getService(ServiceWithValueObject::class);
        $withType = $serviceWithValueObject->getWithType();

        $this->assertInstanceOf(WithType::class, $withType);
        $this->assertInstanceOf(IntegerType::class, $withType->getType());
    }

    public function testInlineValueObjectsFunction(): void
    {
        /** @var ServiceWithValueObject $serviceWithValueObject */
        $serviceWithValueObject = $this->getService(ServiceWithValueObject::class);

        $withTypes = $serviceWithValueObject->getWithTypes();
        $this->assertCount(1, $withTypes);
        $singleWithType = $withTypes[0];
        $this->assertInstanceOf(WithType::class, $singleWithType);
        $this->assertInstanceOf(StringType::class, $singleWithType->getType());
    }
}
