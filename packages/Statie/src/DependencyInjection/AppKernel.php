<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;
use Symplify\Statie\DependencyInjection\CompilerPass\CollectorCompilerPass;

final class AppKernel extends AbstractCliKernel
{
    /**
     * @var string
     */
    private const CONFIG_NAME = 'statie.neon';

    public function __construct()
    {
        parent::__construct(random_int(1, 10000), true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');

        if ($localConfig = $this->getConfigPath()) {
            $loader->load($localConfig);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_statie';
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
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }

    /**
     * @return string|false
     */
    private function getConfigPath()
    {
        $possibleConfigPaths = [
            getcwd() . '/' . self::CONFIG_NAME,
            __DIR__ . '/../../' . self::CONFIG_NAME,
            __DIR__ . '/../../../../' . self::CONFIG_NAME,
        ];

        foreach ($possibleConfigPaths as $possibleConfigPath) {
            if (file_exists($possibleConfigPath)) {
                return $possibleConfigPath;
            }
        }

        return false;
    }
}
