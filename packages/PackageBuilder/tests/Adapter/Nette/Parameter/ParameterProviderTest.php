<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Nette\Parameter;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\PackageBuilder\Adapter\Nette\Parameter\ParameterProvider;

final class ParameterProviderTest extends TestCase
{
    public function test(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/ParameterProviderSource/config.neon'
        );

        $parameterProvider = $container->getByType(ParameterProvider::class);
        $this->assertSame([
            'key' => 'value',
        ], $parameterProvider->provide());
    }

    public function testIncluding(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/ParameterProviderSource/Neon/including-config.neon'
        );

        $parameterProvider = $container->getByType(ParameterProvider::class);

        $this->assertSame([
            'one' => 1,
            'two' => 2,
        ], $parameterProvider->provide());
    }
}
