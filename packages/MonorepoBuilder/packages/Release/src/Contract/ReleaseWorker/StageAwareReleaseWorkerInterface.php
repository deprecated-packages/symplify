<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker;

interface StageAwareReleaseWorkerInterface
{
    public function getStage(): string;
}
