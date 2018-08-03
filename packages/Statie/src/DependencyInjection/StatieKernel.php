<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireDefaultCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicDefaultCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;
use Symplify\PackageBuilder\Yaml\ParameterMergingYamlFileLoader;
use Symplify\Statie\DependencyInjection\CompilerPass\CollectorCompilerPass;

final class StatieKernel extends Kernel
{
    /**
     * @var null|string
     */
    private $configFile;

    public function __construct()
    {
        // random_int is used to prevent container name duplication during tests
        parent::__construct((string) random_int(1, 1000000), false);
    }

    public function bootWithConfig(string $configFile): void
    {
        $this->configFile = $configFile;
        $this->boot();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');

        if ($this->configFile) {
            $loader->load($this->configFile);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_statie_kernel';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_statie_kernel_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireDefaultCompilerPass());
        $containerBuilder->addCompilerPass(new PublicDefaultCompilerPass());
    }

    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $kernelFileLocator = new FileLocator($this);

        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($kernelFileLocator),
            new ParameterMergingYamlFileLoader($container, $kernelFileLocator),
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
