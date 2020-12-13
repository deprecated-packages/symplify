<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class CommentedSingleLineWithoutIndent
{
    public function run()
    {
// return 5000;
        return 1000;
    }
}

?>
-----
<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class CommentedSingleLineWithoutIndent
{
    public function run()
    {
        return 1000;
    }
}

?>
