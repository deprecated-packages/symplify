<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Parameter;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Tests\ContainerFactory;

final class ParameterProviderTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/ParameterProviderSource/config.yml'
        );

        $parameterProvider = $container->get(ParameterProvider::class);
        $this->assertSame([
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
        $container = (new ContainerFactory())->createWithConfig(
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
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/ParameterProviderSource/Neon/including-config.neon'
        );

        $parameterProvider = $container->get(ParameterProvider::class);

        $this->assertSame([
            'one' => 1,
            'two' => 2,
        ], $parameterProvider->provide());
    }
}
