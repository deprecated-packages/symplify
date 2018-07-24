<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\LatteToTwigConverter\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireDefaultCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;

final class LatteToTwigConverterKernel extends AbstractCliKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_latte_to_twig_converter';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_latte_to_twig_converter_logs';
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireDefaultCompilerPass());
    }
}
