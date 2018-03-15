<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;

final class GenericConfigAwareKernel extends Kernel
{
    /**
     * @var string[]
     */
    private $configs = [];

    /**
     * @var string
     */
    private $uniqueHash;

    /**
     * @var CompilerPassInterface[]
     */
    private $compilerPasses = [];

    /**
     * @param string[] $configs
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function __construct(array $configs, array $compilerPasses = [])
    {
        $this->uniqueHash = md5(serialize($configs));
        $this->configs = $configs;
        $this->compilerPasses = $compilerPasses;

        parent::__construct('prod', true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_generic_kernel' .  $this->uniqueHash;
    }

    public function getLogDir(): string
    {
        return $this->getCacheDir() . '_logs';
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());

        foreach ($this->compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }
    }
}
