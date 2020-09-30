<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class SkipFuncCallSingleMethodWithArgs
{
    public function run()
    {
        config()->set('this', 'that');
    }
}
