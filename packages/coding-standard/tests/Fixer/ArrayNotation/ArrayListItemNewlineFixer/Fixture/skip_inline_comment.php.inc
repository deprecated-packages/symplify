<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer\Fixture;

final class SkipInlineComment
{
    public function run()
    {
        return [
            1 => 'The first', // Keep comments here
            3 => 'Another',
        ];
    }
}
