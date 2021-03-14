<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Parameter;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PackageBuilder\Tests\HttpKernel\PackageBuilderTestKernel;

final class ParameterProviderTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernelWithConfigs(
            PackageBuilderTestKernel::class,
            [__DIR__ . '/ParameterProviderSource/config.php']
        );

        $parameterProvider = $this->getService(ParameterProvider::class);

        $parameters = $parameterProvider->provide();
        $this->assertArrayHasKey('key', $parameters);
        $this->assertArrayHasKey('camelCase', $parameters);
        $this->assertArrayHasKey('pascal_case', $parameters);

        $this->assertSame('value', $parameters['key']);
        $this->assertSame('Lion', $parameters['camelCase']);
        $this->assertSame('Celsius', $parameters['pascal_case']);

        $keyParameter = $parameterProvider->provideParameter('key');
        $this->assertSame('value', $keyParameter);

        $parameterProvider->changeParameter('key', 'anotherKey');
        $keyParameter = $parameterProvider->provideParameter('key');
        $this->assertSame('anotherKey', $keyParameter);
    }

    public function testInclude(): void
    {
        $this->bootKernelWithConfigs(
            PackageBuilderTestKernel::class,
            [__DIR__ . '/ParameterProviderSource/Yaml/including-config.php']
        );

        $parameterProvider = $this->getService(ParameterProvider::class);

        $parameters = $parameterProvider->provide();
        $this->assertArrayHasKey('one', $parameters);
        $this->assertArrayHasKey('two', $parameters);

        $this->assertSame(1, $parameters['one']);
        $this->assertSame(2, $parameters['two']);

        $this->assertArrayHasKey('kernel.project_dir', $parameterProvider->provide());
    }
}
