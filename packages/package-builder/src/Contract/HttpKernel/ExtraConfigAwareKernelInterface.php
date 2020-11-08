<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Contract\HttpKernel;

use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

interface ExtraConfigAwareKernelInterface extends KernelInterface
{
    /**
     * @param string[]|SmartFileInfo[] $configs
     */
    public function setConfigs(array $configs): void;
}
