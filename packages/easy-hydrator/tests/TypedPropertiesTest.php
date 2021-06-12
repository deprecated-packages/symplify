<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use Symplify\EasyHydrator\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\Tests\Fixture\TypedProperty;
use Symplify\EasyHydrator\Tests\HttpKernel\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class TypedPropertiesTest extends AbstractKernelTestCase
{
    /**
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    protected function setUp(): void
    {
        $this->bootKernel(EasyHydratorTestKernel::class);

        $this->arrayToValueObjectHydrator = $this->getService(ArrayToValueObjectHydrator::class);
    }

    public function test(): void
    {
        $typedProperty = $this->arrayToValueObjectHydrator->hydrateArray(['value' => 'yay'], TypedProperty::class);

        $this->assertInstanceOf(TypedProperty::class, $typedProperty);

        /** @var TypedProperty $typedProperty */
        $this->assertSame('yay', $typedProperty->getValue());
    }
}
