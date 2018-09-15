<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DependencyInjection;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;
use Symplify\PackageBuilder\Yaml\FileLoader\ParameterImportsYamlFileLoader;

final class TokenRunnerKernel extends Kernel
{
    use SimpleKernelTrait;

    /**
     * @var string
     */
    private $configFile;

    public function __construct(string $configFile)
    {
        parent::__construct('token_runner', true);

        $this->configFile = $configFile;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../src/config/config.yml');
        $loader->load($this->configFile);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $kernelFileLocator = new FileLocator($this);

        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($container, $kernelFileLocator),
            new ParameterImportsYamlFileLoader($container, $kernelFileLocator),
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
