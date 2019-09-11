<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Source\FixerInterface;

class SomeFixer implements FixerInterface
{
    public function secondMethod()
    {

    }

    public function someExtraMethod()
    {

    }

    public function firstMethod()
    {

    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Source\FixerInterface;

class SomeFixer implements FixerInterface
{

    public function firstMethod()
    {

    }
    public function secondMethod()
    {

    }

    public function someExtraMethod()
    {

    }
}

?>
