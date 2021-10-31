<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig;
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

        return $this->create([], [], $configFiles);
    }
}
