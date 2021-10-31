<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        return $this->create([], [], []);
    }
}
