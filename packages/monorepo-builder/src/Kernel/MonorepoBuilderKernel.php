<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Kernel;

use Psr\Container\ContainerInterface;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\MonorepoBuilder\DependencyInjection\RecentTagResolverScopeCompilerPass;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use Symplify\PackageBuilder\ValueObject\ConsoleColorDiffConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class MonorepoBuilderKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;
        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;

        $compilerPasses = [
            new AutowireInterfacesCompilerPass([ReleaseWorkerInterface::class]),
            new RecentTagResolverScopeCompilerPass(),
        ];

        return $this->create($configFiles, $compilerPasses, []);
    }
}
