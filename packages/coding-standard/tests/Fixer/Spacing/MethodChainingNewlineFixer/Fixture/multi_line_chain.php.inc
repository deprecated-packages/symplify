<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class MultiLineChain
{
    public function run()
    {
        return $this->one()->two();
    }

    public function one()
    {
        return $this;
    }

    public function two()
    {
        return $this;
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class MultiLineChain
{
    public function run()
    {
        return $this->one()
->two();
    }

    public function one()
    {
        return $this;
    }

    public function two()
    {
        return $this;
    }
}

?>
