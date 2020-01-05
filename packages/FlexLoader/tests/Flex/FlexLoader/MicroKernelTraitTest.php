<?php

declare(strict_types=1);

namespace Symplify\FlexLoader\Tests\Flex\FlexLoader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symplify\FlexLoader\Tests\Flex\FlexLoader\Source\ExtraService;
use Symplify\FlexLoader\Tests\Flex\FlexLoader\Source\MicroKernelTraitKernel;
use Symplify\FlexLoader\Tests\Flex\FlexLoader\Source\SomeService;

final class MicroKernelTraitTest extends TestCase
{
    public function test(): void
    {
        $microKernelTraitKernel = new MicroKernelTraitKernel('dev', true);
        $microKernelTraitKernel->boot();

        /** @var Container $container */
        $container = $microKernelTraitKernel->getContainer();

        $this->assertTrue($container->has(SomeService::class));
        $this->assertTrue($container->has(ExtraService::class));
        $this->assertTrue($container->hasParameter('default.parameter'));
        $this->assertTrue($container->hasParameter('dev.environment.parameter'));
    }
}
