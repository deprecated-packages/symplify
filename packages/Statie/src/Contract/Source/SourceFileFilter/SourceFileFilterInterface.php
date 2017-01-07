<?php

declare(strict_types=1);

namespace Symplify\Statie\Contract\Source\SourceFileFilter;

use SplFileInfo;

interface SourceFileFilterInterface
{
    public function getName() : string;

    public function matchesFileSource(SplFileInfo $fileInfo) : bool;
}
