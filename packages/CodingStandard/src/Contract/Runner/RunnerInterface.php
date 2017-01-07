<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Contract\Runner;

interface RunnerInterface
{
    public function runForDirectory(string $directory) : string;

    public function fixDirectory(string $directory) : string;

    public function hasErrors() : bool;
}
