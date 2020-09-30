<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class SkipMultiLineChain
{
    public function run()
    {
        return $this->one()
            ->two();
    }

    public function one()
    {
        return $this;
    }

    public function two()
    {
        return $this;
    }
}
