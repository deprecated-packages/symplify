<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class BetterPhpDocParserKernel extends Kernel
{
    use SimpleKernelTrait;

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
