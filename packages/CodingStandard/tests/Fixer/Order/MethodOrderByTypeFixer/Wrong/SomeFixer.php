<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Wrong;

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
