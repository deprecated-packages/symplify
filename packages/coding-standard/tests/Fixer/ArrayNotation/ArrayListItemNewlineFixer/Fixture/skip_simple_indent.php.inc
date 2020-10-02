<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer\Fixture;

final class SkipSimpleIndent
{
    public function run()
    {
        return [
            1 => [
                'node' => '',
                'children' => [
                    'node' => '',
                ],
            ],
        ];
    }
}
