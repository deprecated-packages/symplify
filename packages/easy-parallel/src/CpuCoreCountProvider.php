<?php

declare(strict_types=1);

namespace Symplify\EasyParallel;

use Fidry\CpuCounter\CpuCoreCounter;
use Fidry\CpuCounter\NumberOfCpuCoreNotFound;

final class CpuCoreCountProvider
{
    /**
     * @var int
     */
    private const DEFAULT_CORE_COUNT = 2;

    public function provide(): int
    {
        try {
            return (new CpuCoreCounter())->getCount();
        } catch (NumberOfCpuCoreNotFound) {
            return self::DEFAULT_CORE_COUNT;
        }
    }
}
