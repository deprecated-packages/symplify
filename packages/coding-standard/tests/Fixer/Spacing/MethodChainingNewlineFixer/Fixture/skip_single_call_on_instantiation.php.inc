<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class SkipSingleCallOnInstantiation
{
    public function run()
    {
        return (new self())->one();
    }

    public function one()
    {
        return $this;
    }
}
