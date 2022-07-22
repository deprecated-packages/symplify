<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedThisType;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\ThisPassedFromInterface;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\SomeInterface;

final class CallByThisFromInterface implements SomeInterface
{
    public function run(ThisPassedFromInterface $thisPassedFromInterface): void
    {
        $thisPassedFromInterface->run($this);
    }
}
