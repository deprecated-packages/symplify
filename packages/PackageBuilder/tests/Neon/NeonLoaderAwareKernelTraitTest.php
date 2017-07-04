<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Neon;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\DependencyInjection\Container;
use Symplify\PackageBuilder\Tests\Neon\NeonLoaderAwareKernelTraitSource\KernelWithNeonLoaderAwareTrait;
use Symplify\PackageBuilder\Tests\Neon\NeonLoaderAwareKernelTraitSource\NeonFreeKernel;

final class NeonLoaderAwareKernelTraitTest extends TestCase
{
    public function testNeonFreeKernel(): void
    {
        $this->expectException(FileLoaderLoadException::class);
        $kernel = new NeonFreeKernel(__DIR__ . '/NeonLoaderAwareKernelTraitSource/config.neon');
        $kernel->boot();
    }

    public function test(): void
    {
        $kernel = new KernelWithNeonLoaderAwareTrait(__DIR__ . '/NeonLoaderAwareKernelTraitSource/config.neon');
        $kernel->boot();

        $container = $kernel->getContainer();
        $this->assertInstanceOf(Container::class, $container);
        $this->assertSame('value', $container->getParameter('key'));
    }
}
