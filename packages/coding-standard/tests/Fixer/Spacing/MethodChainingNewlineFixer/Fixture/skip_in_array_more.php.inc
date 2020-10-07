<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use PharIo\Version\Version;

class SkipInArrayMore
{
    public function run(Version $version, array $values)
    {
        $values[$version->getMajor()->getValue()];
    }
}
