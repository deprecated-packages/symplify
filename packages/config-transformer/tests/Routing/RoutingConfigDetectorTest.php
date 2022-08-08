<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Routing;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ConfigTransformer\Routing\RoutingConfigDetector;

final class RoutingConfigDetectorTest extends TestCase
{
    private RoutingConfigDetector $routingConfigDetector;

    protected function setUp(): void
    {
        $this->routingConfigDetector = new RoutingConfigDetector();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, bool $expectedResult): void
    {
        $isRoutingFilePath = $this->routingConfigDetector->isRoutingFilePath($filePath);
        $this->assertSame($expectedResult, $isRoutingFilePath);
    }

    /**
     * @return Iterator<string[]|bool[]>
     */
    public function provideData(): Iterator
    {
        yield ['my_app/config/routes.yaml', true];
        yield ['my_app/config/routing.yaml', true];
        yield ['my_app/config/routes/my_packages.yaml', true];
        yield ['my_app/config/routes/prod/some_prod_route.yaml', true];
        yield ['my_app/config/packages/routing.yaml', false];
    }
}
