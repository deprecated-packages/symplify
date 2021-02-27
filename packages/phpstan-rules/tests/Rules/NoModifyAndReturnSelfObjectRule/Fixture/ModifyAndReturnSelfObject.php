<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\Source\SomeObjectToReturn;

final class ModifyAndReturnSelfObject
{
    public function run(SomeObjectToReturn $someObjectToReturn)
    {
        $this->process($someObjectToReturn);
        return $someObjectToReturn;
    }
}
