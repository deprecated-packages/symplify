<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class SkipKeywords
{
    public function run($firstTag, $secondTag)
    {
        // for imports
        return $firstTag;
    }
}
