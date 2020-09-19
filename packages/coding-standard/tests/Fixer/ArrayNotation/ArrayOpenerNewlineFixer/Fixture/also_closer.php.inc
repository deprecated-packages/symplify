<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerNewlineFixer\Fixture;

final class AlsoCloser
{
    public function run()
    {
        $value = [
            SingleStaticServiceToDynamicRector::CLASS_TYPES => [
                'SomeClass']
        ];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerNewlineFixer\Fixture;

final class AlsoCloser
{
    public function run()
    {
        $value = [
            SingleStaticServiceToDynamicRector::CLASS_TYPES => [
                'SomeClass'
]
        ];
    }
}

?>
