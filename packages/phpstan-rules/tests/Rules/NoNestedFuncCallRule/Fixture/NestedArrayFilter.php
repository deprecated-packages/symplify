<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class NestedArrayFilter
{
    public function run(array $paragraphs)
    {
        $lineWrappedParagraphs = array_map(
            function (string $paragraph): string {
                return wordwrap($paragraph,1000);
            },
            $paragraphs
        );
    }
}
