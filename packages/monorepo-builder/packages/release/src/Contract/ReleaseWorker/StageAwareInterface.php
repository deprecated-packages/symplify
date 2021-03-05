<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker;

interface StageAwareInterface extends ReleaseWorkerInterface
{
    /**
     * Set name of the stage, so workers can be filtered by --stage option: e.g "vendor/bin/monorepo-builder release
     * v5.0.0 --stage <name>"
     */
    public function getStage(): string;
}
