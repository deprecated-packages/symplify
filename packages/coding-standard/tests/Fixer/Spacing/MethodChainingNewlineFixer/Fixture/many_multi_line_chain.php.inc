<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Source\SomeClassWithChains;

class ManyMultiLineChain
{
    public function run(SomeClassWithChains $someClassWithChains)
    {
        return $someClassWithChains->one()->two()->three();
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Source\SomeClassWithChains;

class ManyMultiLineChain
{
    public function run(SomeClassWithChains $someClassWithChains)
    {
        return $someClassWithChains->one()
->two()
->three();
    }
}

?>
