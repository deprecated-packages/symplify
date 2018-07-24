<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ChangelogLinker\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\ChangelogLinker\DependencyInjection\CompilerPass\DetectParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireDefaultCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;

final class ChangelogLinkerKernel extends AbstractCliKernel
{
    /**
     * @var null|string
     */
    private $configFile;

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');

        if ($this->configFile) {
            $loader->load($this->configFile);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_changelog_linker';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_changelog_linker_logs';
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
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
        $containerBuilder->addCompilerPass(new DetectParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireDefaultCompilerPass());
    }
}
