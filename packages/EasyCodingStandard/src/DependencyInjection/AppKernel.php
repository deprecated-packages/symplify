<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;
use Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait;

final class AppKernel extends AbstractCliKernel
{
    use NeonLoaderAwareKernelTrait;

    /**
     * @var string[]
     */
    private $configFiles = [];

    /**
     * @param string[]|null $configFiles
     */
    public function __construct(?array $configFiles = [])
    {
        $this->configFiles = $configFiles;
        parent::__construct();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');

        foreach ($this->configFiles as $configFile) {
            $this->registerLocalConfig($loader, $configFile);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_easy_coding_standard';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new CheckersBundle,
        ];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }
}
