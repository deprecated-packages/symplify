<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass;

use Symplify\AutowireArrayParameter\Tests\HttpKernel\AutowireArrayParameterHttpKernel;
use Symplify\AutowireArrayParameter\Tests\Source\SomeCollector;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class AutowireArrayParameterCompilerPassTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernel(AutowireArrayParameterHttpKernel::class);

        /** @var SomeCollector $someCollector */
        $someCollector = self::$container->get(SomeCollector::class);
        $this->assertCount(2, $someCollector->getCollected());
    }
}
