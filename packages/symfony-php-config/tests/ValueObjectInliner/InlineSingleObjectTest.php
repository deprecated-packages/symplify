<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\Source\SomeValueObject;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

final class InlineSingleObjectTest extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(object $valueObject, string $expectedType): void
    {
        $servicesConfigurator = $this->createServiceConfigurator();

        $referenceConfigurator = ValueObjectInliner::inlineArgumentObject($valueObject, $servicesConfigurator);

        $this->assertInstanceOf(ReferenceConfigurator::class, $referenceConfigurator);

        $id = (string) $referenceConfigurator;
        $this->assertSame($expectedType, $id);
    }

    public function provideData(): Iterator
    {
        yield [new SomeValueObject('Rector'), SomeValueObject::class];
    }

    private function createServiceConfigurator(): ServicesConfigurator
    {
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());

        $instanceOf = [];

        return new ServicesConfigurator($containerBuilder, $phpFileLoader, $instanceOf);
    }
}
