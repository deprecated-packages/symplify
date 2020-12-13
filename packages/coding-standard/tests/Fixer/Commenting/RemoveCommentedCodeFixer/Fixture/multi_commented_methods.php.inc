<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class MultiCommentedMethods
{
//    public function run2()
//    {
//    }

    public function fun()
    {
    }

}

?>
-----
<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

final class MultiCommentedMethods
{

    public function fun()
    {
    }

}

?>
