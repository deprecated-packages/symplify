<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Symfony\Parameter\ParameterProvider;
use Symplify\Statie\DependencyInjection\ContainerFactory;

final class ConfigurationTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory)->createWithConfig(__DIR__ . '/ConfigurationSource/config.neon');

        /** @var ParameterProvider $parameterProvider */
        $parameterProvider = $container->get(ParameterProvider::class);
        $this->assertSame([
            'another_key' => 'another_value',
            'key' => 'value',
        ], $parameterProvider->provide());
    }
}
