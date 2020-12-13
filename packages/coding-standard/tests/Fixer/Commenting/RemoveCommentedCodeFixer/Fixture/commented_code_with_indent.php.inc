<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class CommentedCodeWithIndent
{
    //    public function run()
    //    {
    //    }
}

?>
-----
<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class CommentedCodeWithIndent
{
}

?>
