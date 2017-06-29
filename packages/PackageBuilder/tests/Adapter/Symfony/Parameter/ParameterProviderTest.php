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
}
