<?php

declare(strict_types=1);

namespace Symplify\Skipper\Contract;

use Symplify\SmartFileSystem\SmartFileInfo;

interface SkipVoterInterface
{
    public function match(string | object $element): bool;

    public function shouldSkip(string | object $element, SmartFileInfo $smartFileInfo): bool;
}
