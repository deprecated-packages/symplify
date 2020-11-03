<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class HasDifferentParameterWithParentMethod extends ParentClass
{
    public function execute($string, $int)
    {
    }
}
