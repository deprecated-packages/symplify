<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use PharIo\Version\Version;

class SkipInArray
{
    public function run(Version $version)
    {
        return [$version->getMajor()->getValue()];
    }
}
