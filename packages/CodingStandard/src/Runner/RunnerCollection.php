<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Runner;

use Symplify\CodingStandard\Contract\Runner\RunnerCollectionInterface;
use Symplify\CodingStandard\Contract\Runner\RunnerInterface;

final class RunnerCollection implements RunnerCollectionInterface
{
    /**
     * @var RunnerInterface[]
     */
    private $runners = [];

    public function addRunner(RunnerInterface $runner)
    {
        $this->runners[] = $runner;
    }

    /**
     * @return RunnerInterface[]
     */
    public function getRunners() : array
    {
        return $this->runners;
    }
}
