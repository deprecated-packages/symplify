<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Mostly for tests
 */
final class GenericContainerFactory
{
    /**
     * @param string[] $configs
     */
    public function createWithConfigs(array $configs): ContainerInterface
    {
        $genericKernel = new GenericConfigAwareKernel($configs);
        $genericKernel->boot();

        return $genericKernel->getContainer();
    }

    /**
     * @param string[] $configs
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function createWithConfigsAndCompilerPasses(array $configs, array $compilerPasses): ContainerInterface
    {
        $genericKernel = new GenericConfigAwareKernel($configs, $compilerPasses);
        $genericKernel->boot();

        return $genericKernel->getContainer();
    }
}
