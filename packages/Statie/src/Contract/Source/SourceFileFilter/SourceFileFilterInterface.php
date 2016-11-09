<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Contract\Source\SourceFileFilter;

use SplFileInfo;

interface SourceFileFilterInterface
{
    public function getName() : string;

    public function matchesFileSource(SplFileInfo $fileInfo) : bool;
}
