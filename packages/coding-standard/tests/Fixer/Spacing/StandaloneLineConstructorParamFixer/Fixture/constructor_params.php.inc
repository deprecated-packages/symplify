<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLinePromotedPropertyFixer\Fixture;

final class ConstructorParams
{
    public function __construct(int $age, string $name)
    {
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLinePromotedPropertyFixer\Fixture;

final class ConstructorParams
{
    public function __construct(
        int $age,
        string $name
    )
    {
    }
}

?>
