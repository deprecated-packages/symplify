<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner;

use PHPStan\Type\UnionType;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SymfonyPhpConfig\Tests\HttpKernel\SymfonyPhpConfigKernel;
use Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\Source\ServiceWithValueObject;
use Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\Source\WithType;

final class ConfigFactoryNestedTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(SymfonyPhpConfigKernel::class, [
            __DIR__ . '/config/config_with_nested_union_type_value_objects.php',
        ]);
    }

    public function testInlineValueObjectFunction(): void
    {
        /** @var ServiceWithValueObject $serviceWithValueObject */
        $serviceWithValueObject = self::$container->get(ServiceWithValueObject::class);
        $withType = $serviceWithValueObject->getWithType();

        $this->assertInstanceOf(WithType::class, $withType);
        $this->assertInstanceOf(UnionType::class, $withType->getType());
    }
}
