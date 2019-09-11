<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Contract\HttpKernel;

interface ExtraConfigAwareKernelInterface
{
    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void;
}
