<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineConstructorParamFixer\FixtureContraindications;

final class CombinationWithLineLenghtFixer
{
    public function __construct($name, $type)
    {
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineConstructorParamFixer\FixtureContraindications;

final class CombinationWithLineLenghtFixer
{
    public function __construct(
        $name,
        $type
    ) {
    }
}

?>
