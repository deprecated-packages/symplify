<?php

declare(strict_types=1);

namespace Symplify\NeonConfigDumper\Kernel;

use Psr\Container\ContainerInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class NeonConfigDumperKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';

        return $this->create($configFiles, [], []);
    }
}
