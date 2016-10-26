<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Contract\Process;

use Symfony\Component\Process\Process;

interface ProcessBuilderInterface
{
    public function getProcess() : Process;
}
