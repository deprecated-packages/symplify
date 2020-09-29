<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use PharIo\Version\Version;

class SkipConcat
{
    public function run(Version $lastStableVersion)
    {
        return $this->symplifyVersionToRequire = '^' . $lastStableVersion->getMajor()->getValue() . '.' . $lastStableVersion->getMinor()->getValue();
    }
}
