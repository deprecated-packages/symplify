<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

class HasDifferentParameterWithParentMethod extends ParentClass
{
    public function execute($string, $int)
    {
    }
}
