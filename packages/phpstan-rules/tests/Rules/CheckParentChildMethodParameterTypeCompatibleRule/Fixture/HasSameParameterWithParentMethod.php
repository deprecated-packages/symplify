<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class HasSameParameterWithParentMethod extends ParentClass
{
    public function execute(string $string, int $int)
    {
    }
}
