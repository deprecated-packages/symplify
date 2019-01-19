<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass\Source\AutoBindParametersKernel;
use Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass\Source\ServiceWithAutowiredParameter;

final class AutoBindParametersCompilerPassTest extends TestCase
{
    public function test(): void
    {
        $autoBindParametersKernel = new AutoBindParametersKernel();
        $autoBindParametersKernel->boot();

        $container = $autoBindParametersKernel->getContainer();
        $serviceWithAutowiredParameter = $container->get(ServiceWithAutowiredParameter::class);

        $this->assertSame('value', $serviceWithAutowiredParameter->getSomeParameter());
    }
}
