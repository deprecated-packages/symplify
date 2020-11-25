<?php

declare(strict_types=1);

namespace Symplify\Skipper\SkipVoter;

use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassAndCodeSkipVoter implements SkipVoterInterface
{
    /**
     * @param string|object $element
     */
    public function match($element): bool
    {
        return false;
        dump($element);
        die;
    }

    /**
     * @param string|object $element
     */
    public function shouldSkip($element, SmartFileInfo $smartFileInfo): bool
    {
        dump($element);
        die;
    }
}
