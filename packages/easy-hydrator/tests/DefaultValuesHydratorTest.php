<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use Symplify\EasyHydrator\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\Exception\MissingDataException;
use Symplify\EasyHydrator\Tests\Fixture\DefaultValuesConstructor;
use Symplify\EasyHydrator\Tests\HttpKernel\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class DefaultValuesHydratorTest extends AbstractKernelTestCase
{
    /**
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    protected function setUp(): void
    {
        $this->bootKernel(EasyHydratorTestKernel::class);

        $this->arrayToValueObjectHydrator = self::$container->get(ArrayToValueObjectHydrator::class);
    }

    public function testExceptionWillBeThrownWhenMissingDataForNonOptionalParameter(): void
    {
        $this->expectException(MissingDataException::class);

        $this->arrayToValueObjectHydrator->hydrateArray([], DefaultValuesConstructor::class);
    }

    public function testDefaultValues(): void
    {
        $data = [
            'foo' => null,
            'bar' => 'baz',
        ];

        /** @var DefaultValuesConstructor $object */
        $object = $this->arrayToValueObjectHydrator->hydrateArray($data, DefaultValuesConstructor::class);

        self::assertNull($object->getFoo());
        self::assertNull($object->getPerson());
        self::assertSame('baz', $object->getBar());
    }
}
