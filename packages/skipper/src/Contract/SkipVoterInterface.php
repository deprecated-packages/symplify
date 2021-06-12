<?php

declare(strict_types=1);

namespace Symplify\Skipper\Contract;

use Symplify\SmartFileSystem\SmartFileInfo;

interface SkipVoterInterface
{
    /**
     * @param string|object $element
     */
    public function match(string | object $element): bool;

    /**
     * @param string|object $element
     */
    public function shouldSkip(string | object $element, SmartFileInfo $smartFileInfo): bool;
}
