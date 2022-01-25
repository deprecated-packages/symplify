<?php

declare(strict_types=1);

namespace Symplify\Skipper\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\Skipper\ValueObject\SkipperConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class SkipperKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = SkipperConfig::FILE_PATH;
        return $this->create($configFiles, [], []);
    }
}
