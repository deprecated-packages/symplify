<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer\Fixture;

final class AlsoCloser
{
    public function run()
    {
        $value = [SingleStaticServiceToDynamicRector::CLASS_TYPES => ['SomeClass']];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer\Fixture;

final class AlsoCloser
{
    public function run()
    {
        $value = [
SingleStaticServiceToDynamicRector::CLASS_TYPES => ['SomeClass']
];
    }
}

?>
