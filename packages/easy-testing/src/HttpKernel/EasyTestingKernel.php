<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = EasyTestingConfig::FILE_PATH;
        return $this->create([], [], $configFiles);
    }
}
