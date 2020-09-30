<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class SkipFuncCallArgsSingleMethod
{
    public function run()
    {
        config('here', [])->set('this', 'that');
    }
}
