<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class SymfonyPhpConfigKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        return $this->create([], [], $configFiles);
    }
}
