<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Contract\Runner;

interface RunnerCollectionInterface
{
    public function addRunner(RunnerInterface $runner) : void;

    /**
     * @return RunnerInterface[]
     */
    public function getRunners() : array;
}
