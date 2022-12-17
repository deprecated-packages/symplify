<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonManipulator\Tests\Kernel;

use Psr\Container\ContainerInterface;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class ComposerJsonManipulatorKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;

        return $this->create($configFiles);
    }
}
