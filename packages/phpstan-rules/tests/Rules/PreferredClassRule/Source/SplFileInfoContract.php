<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassRule\Source;

use SplFileInfo;

interface SplFileInfoContract
{
    public function process(SplFileInfo $splFileInfo);
}
