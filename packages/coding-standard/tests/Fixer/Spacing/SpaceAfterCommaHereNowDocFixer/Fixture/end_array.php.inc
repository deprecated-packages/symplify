<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer\Fixture;

class ExtraSpace
{
    public function run()
    {
        $values = [
            <<<RECTIFY
First
RECTIFY];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer\Fixture;

class ExtraSpace
{
    public function run()
    {
        $values = [
            <<<RECTIFY
First
RECTIFY
];
    }
}

?>
