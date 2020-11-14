<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class SkipVariableAndOther
{
    public function run()
    {
        // $value is first, instead of type is first
        return 1000;
    }
}
