<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class SkipFuncCallWithMethodCall
{
    public function run($one, $two)
    {
        return inline_service($one)->args($two);
    }
}
