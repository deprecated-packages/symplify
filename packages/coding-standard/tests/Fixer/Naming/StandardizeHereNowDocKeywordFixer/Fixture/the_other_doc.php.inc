<?php

namespace Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer\Fixture;

class SomeClass
{
    public const SOME_EXAMPLE = <<<'RECTIFY'
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
}

?>
