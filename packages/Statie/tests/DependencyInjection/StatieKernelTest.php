<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\DependencyInjection\ContainerFactory;

final class StatieKernelTest extends TestCase
{
    public function testMergeParameters(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/StatieKernelSource/parameter-merge.yml'
        );
        $parameterProvider = $container->get(ParameterProvider::class);

        $this->assertCount(2, $parameterProvider->provideParameter('framework'));
    }
}
