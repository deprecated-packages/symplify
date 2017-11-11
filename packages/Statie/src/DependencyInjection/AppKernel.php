<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;
use Symplify\Statie\DependencyInjection\CompilerPass\CollectorCompilerPass;

final class AppKernel extends AbstractCliKernel
{
    /**
     * @var null|string
     */
    private $configFile;

    public function __construct(?string $configConfig = '')
    {
        $this->configFile = $configConfig;
        parent::__construct();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');

        if ($this->configFile) {
            $loader->load($this->configFile, ['parameters', 'includes', 'services']);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_statie_kernel';
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
    }
}
