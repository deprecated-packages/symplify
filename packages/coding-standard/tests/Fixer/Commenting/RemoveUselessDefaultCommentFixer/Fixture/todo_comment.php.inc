<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemovePHPStormTodoImplementMethodCommentFixer\Fixture;

class PackagesData
{
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }
}
?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemovePHPStormTodoImplementMethodCommentFixer\Fixture;

class PackagesData
{
    public function getIterator()
    {
        
    }
}
?>
