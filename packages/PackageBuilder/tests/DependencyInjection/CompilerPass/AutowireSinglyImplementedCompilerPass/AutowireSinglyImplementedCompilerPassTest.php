<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass\Source\AutowireSinglyImplementedInterfaceKernel;
use Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass\Source\ServiceB;
use Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass\Source\ServiceInterface;

class AutowireSinglyImplementedCompilerPassTest extends TestCase
{
    public function test(): void
    {
        $autoBindParametersKernel = new AutowireSinglyImplementedInterfaceKernel();
        $autoBindParametersKernel->boot();

        $container = $autoBindParametersKernel->getContainer();
        $service = $container->get(ServiceInterface::class);

        $this->assertInstanceOf(ServiceB::class, $service);
    }
}
