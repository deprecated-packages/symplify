<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ChangelogLinker\DependencyInjection\CompilerPass\DetectParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ConfigurableCollectorCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class ChangelogLinkerKernel extends Kernel
{
    use SimpleKernelTrait;

    /**
     * @var string|null
     */
    private $configFile;

    public function __construct(?string $configFile = null)
    {
        $this->configFile = $configFile;
        $configFilesHash = $configFile ? '_' . md5($configFile) : '';

        parent::__construct('changelog_linker' . $configFilesHash, true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');

        if ($this->configFile) {
            $loader->load($this->configFile);
        }
    }

    public function bootWithConfig(string $config): void
    {
        $this->configFile = $config;
        $this->boot();
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
        $containerBuilder->addCompilerPass(new ConfigurableCollectorCompilerPass());
        $containerBuilder->addCompilerPass(new DetectParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
    }
}
