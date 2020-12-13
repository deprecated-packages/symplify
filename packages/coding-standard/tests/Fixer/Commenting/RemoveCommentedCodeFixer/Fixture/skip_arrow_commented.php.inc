<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class SkipArrowCommented
{
    public function run($firstTag, $secondTag)
    {
        // -1 => put higher first
        return -1 * version_compare($firstTag, $secondTag);
    }
}
