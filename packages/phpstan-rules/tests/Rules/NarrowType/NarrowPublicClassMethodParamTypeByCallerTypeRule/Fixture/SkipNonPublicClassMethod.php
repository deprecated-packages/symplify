<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

final class SkipNonPublicClassMethod
{
    public function personInTree()
    {
        $this->callMe(1000);
    }

    private function callMe($number)
    {
    }
}
