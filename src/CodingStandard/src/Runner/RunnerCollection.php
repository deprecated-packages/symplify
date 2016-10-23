<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

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
