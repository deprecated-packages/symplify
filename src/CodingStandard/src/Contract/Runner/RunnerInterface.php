<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Contract\Runner;

interface RunnerInterface
{
    public function runForDirectory(string $directory) : string;

    public function fixDirectory(string $directory) : string;

    public function hasErrors() : bool;
}
