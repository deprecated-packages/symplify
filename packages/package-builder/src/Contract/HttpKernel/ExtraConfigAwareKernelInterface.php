<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Contract\HttpKernel;

use Symfony\Component\HttpKernel\KernelInterface;

interface ExtraConfigAwareKernelInterface extends KernelInterface
{
    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void;
}
