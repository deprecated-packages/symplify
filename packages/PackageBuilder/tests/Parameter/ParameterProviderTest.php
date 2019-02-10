<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Parameter;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\PackageBuilder\Tests\HttpKernel\PackageBuilderTestKernel;

final class ParameterProviderTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernelWithConfigs(
            PackageBuilderTestKernel::class,
            [__DIR__ . '/ParameterProviderSource/config.yml']
        );

        $parameterProvider = self::$container->get(ParameterProvider::class);
        $this->assertContains([
            'key' => 'value',
            'camelCase' => 'Lion',
            'pascal_case' => 'Celsius',
        ], $parameterProvider->provide());

        $this->assertSame('value', $parameterProvider->provideParameter('key'));

        $parameterProvider->changeParameter('key', 'anotherKey');
        $this->assertSame('anotherKey', $parameterProvider->provideParameter('key'));
    }

    public function testIncludingYaml(): void
    {
        $this->bootKernelWithConfigs(
            PackageBuilderTestKernel::class,
            [__DIR__ . '/ParameterProviderSource/Yaml/including-config.yml']
        );

        $parameterProvider = self::$container->get(ParameterProvider::class);

        $this->assertContains([
            'one' => 1,
            'two' => 2,
        ], $parameterProvider->provide());

        $this->assertArrayHasKey('kernel.root_dir', $parameterProvider->provide());
    }
}
