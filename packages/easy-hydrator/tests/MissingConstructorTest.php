<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use Symplify\EasyHydrator\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\Exception\MissingConstructorException;
use Symplify\EasyHydrator\Tests\Fixture\NoConstructor;
use Symplify\EasyHydrator\Tests\HttpKernel\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class MissingConstructorTest extends AbstractKernelTestCase
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

    public function test(): void
    {
        $this->expectException(MissingConstructorException::class);

        $this->arrayToValueObjectHydrator->hydrateArray([
            'key' => 'whatever',
        ], NoConstructor::class);
    }
}
