<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer\Fixture;

class ExtraSpace
{
    public function run()
    {
        $values = [
            <<<RECTIFY
First
RECTIFY,
            1000
        ];
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
,
            1000
        ];
    }
}

?>
