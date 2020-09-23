<?php

namespace Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer\Fixture;

class SomeClass
{
    public const SOME_EXAMPLE = <<<'RECTIFY'
    Some code
    Text
    RECTIFY;

    public const SOME_EXAMPLE_2 = <<<'RECTIFY'
    Some code
    Text
    RECTIFY;
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer\Fixture;

class SomeClass
{
    public const SOME_EXAMPLE = <<<'CODE_SAMPLE'
    Some code
    Text
    CODE_SAMPLE;

    public const SOME_EXAMPLE_2 = <<<'CODE_SAMPLE'
    Some code
    Text
    CODE_SAMPLE;
}

?>
