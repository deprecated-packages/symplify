<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use PharIo\Version\Version;

class SkipInCall
{
    public function run(Version $version)
    {
        return $this->again($version->getMajor()->getValue());
    }

    public function again()
    {
    }
}
