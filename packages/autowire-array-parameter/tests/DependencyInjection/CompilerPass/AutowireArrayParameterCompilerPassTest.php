<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass;

use Symplify\AutowireArrayParameter\Tests\HttpKernel\AutowireArrayParameterHttpKernel;
use Symplify\AutowireArrayParameter\Tests\Source\ArrayShapeCollector;
use Symplify\AutowireArrayParameter\Tests\Source\IterableCollector;
use Symplify\AutowireArrayParameter\Tests\Source\SomeCollector;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class AutowireArrayParameterCompilerPassTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(AutowireArrayParameterHttpKernel::class);
    }

    public function test(): void
    {
        /** @var SomeCollector $someCollector */
        $someCollector = $this->getService(SomeCollector::class);
        $this->assertCount(2, $someCollector->getCollected());
    }

    public function testArrayShape(): void
    {
        $arrayShapeCollector = $this->getService(ArrayShapeCollector::class);
        $this->assertCount(2, $arrayShapeCollector->getCollected());
    }

    public function testIterable(): void
    {
        $iterableCollector = $this->getService(IterableCollector::class);
        $this->assertCount(2, $iterableCollector->getCollected());
    }
}
