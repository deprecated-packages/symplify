<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Source;

final class SomeClassWithChains
{
    public function one()
    {
        return $this;
    }

    public function two()
    {
        return $this;
    }

    public function three()
    {
        return $this;
    }

    public function four()
    {
        return $this;
    }
}
