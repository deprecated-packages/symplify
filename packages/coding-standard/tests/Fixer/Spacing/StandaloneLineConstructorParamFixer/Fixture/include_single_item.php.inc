<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineConstructorParamFixer\Fixture;

final class IncludeSingleItem
{
    public function __construct(int $age)
    {
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineConstructorParamFixer\Fixture;

final class IncludeSingleItem
{
    public function __construct(
        int $age
    )
    {
    }
}

?>
