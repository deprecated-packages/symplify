<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Source\SomeClassWithChains;

class MultiLineChain
{
    public function run(SomeClassWithChains $someClassWithChains)
    {
        return $someClassWithChains->one()->two();
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Source\SomeClassWithChains;

class MultiLineChain
{
    public function run(SomeClassWithChains $someClassWithChains)
    {
        return $someClassWithChains->one()
->two();
    }
}

?>
