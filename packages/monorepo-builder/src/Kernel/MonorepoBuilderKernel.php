<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Kernel;

use Psr\Container\ContainerInterface;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class MonorepoBuilderKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles = array_merge(
            [
                __DIR__ . '/../../config/config.php',
                ComposerJsonManipulatorConfig::FILE_PATH,
                ConsoleColorDiffConfig::FILE_PATH,
            ],
            $configFiles
        );

        $autowireInterfacesCompilerPass = new AutowireInterfacesCompilerPass([ReleaseWorkerInterface::class]);
        $compilerPasses = [$autowireInterfacesCompilerPass];

        return $this->create([], $compilerPasses, $configFiles);
    }
}
