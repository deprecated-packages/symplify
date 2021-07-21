<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Fixture;

class SomeClass
{
    public function getData()
    {
        // Added an extra space after return
        return  [
            'a' => 'b',
            'c' => 'd',
        ];
    }
}
