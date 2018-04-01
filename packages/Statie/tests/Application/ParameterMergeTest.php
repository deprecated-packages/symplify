<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\DependencyInjection\ContainerFactory;

final class ParameterMergeTest extends TestCase
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/StatieApplicationSource/parameter-merge.yml'
        );
        $this->parameterProvider = $container->get(ParameterProvider::class);
    }

    public function test(): void
    {
        $this->assertCount(2, $this->parameterProvider->provideParameter('framework'));
    }
}
