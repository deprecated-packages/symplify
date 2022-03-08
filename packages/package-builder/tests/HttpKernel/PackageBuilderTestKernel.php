<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class PackageBuilderTestKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../config/test_config.php';
        return $this->create($configFiles);
    }
}
