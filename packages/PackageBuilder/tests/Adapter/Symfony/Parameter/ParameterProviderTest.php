<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Symfony\Parameter;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Symfony\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Tests\Adapter\Symfony\ContainerFactory;

final class ParameterProviderTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/ParameterProviderSource/config.yml'
        );

        $parameterProvider = $container->get(ParameterProvider::class);
        $this->assertSame([
            'key' => 'value',
        ], $parameterProvider->provide());
    }

    public function testParameterLowerCasing(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/ParameterProviderSource/Neon/casing-config.neon'
        );

        $parameterProvider = $container->get(ParameterProvider::class);
        $this->assertSame([
            'camelCase' => 'Lion',
            'pascal_case' => 'Celsius',
        ], $parameterProvider->provide());
    }

    public function testIncludingYaml(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/ParameterProviderSource/Yaml/including-config.yml'
        );

        $parameterProvider = $container->get(ParameterProvider::class);

        $this->assertSame([
            'one' => 1,
            'two' => 2,
        ], $parameterProvider->provide());
    }

    public function testIncludingNeon(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/ParameterProviderSource/Neon/including-config.neon'
        );

        $parameterProvider = $container->get(ParameterProvider::class);

        $this->assertSame([
            'one' => 1,
            'two' => 2,
        ], $parameterProvider->provide());
    }
}
