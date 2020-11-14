<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class SkipQuotedArray
{
    public function run($firstTag)
    {
        // "array"()
        return $firstTag;
    }
}
