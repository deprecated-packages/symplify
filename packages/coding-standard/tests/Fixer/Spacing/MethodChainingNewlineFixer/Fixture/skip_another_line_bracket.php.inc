<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Source\SomeClassWithChains;

class SkipAnotherLineBracket
{
    public function run(SomeClassWithChains $someClassWithChains)
    {
        return $someClassWithChains->two([
                'hey' => 'hou'
            ])->three();
    }
}
