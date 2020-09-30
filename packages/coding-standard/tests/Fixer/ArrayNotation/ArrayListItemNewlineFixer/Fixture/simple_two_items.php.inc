<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer\Fixture;

final class SimpleTwoItems
{
    public function run()
    {
        $value = ['simple' => 1, 'easy' => 2];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer\Fixture;

final class SimpleTwoItems
{
    public function run()
    {
        $value = ['simple' => 1,
'easy' => 2];
    }
}

?>
