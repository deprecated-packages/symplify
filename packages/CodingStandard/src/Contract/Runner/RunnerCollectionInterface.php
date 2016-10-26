<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Contract\Runner;

interface RunnerCollectionInterface
{
    public function addRunner(RunnerInterface $runner);

    /**
     * @return RunnerInterface[]
     */
    public function getRunners() : array;
}
