<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class SkipFuncCallSingleMethod
{
    public function run()
    {
        return app()->one();
    }
}
