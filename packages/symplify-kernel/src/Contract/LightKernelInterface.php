<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Contract;

use Psr\Container\ContainerInterface;

/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface;

    public function getContainer(): ContainerInterface;
}
