<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedType;

use stdClass;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipUsedInternallyForSecondType;

final class OnlyFirstTypeCalledOutside
{
    public function run(SkipUsedInternallyForSecondType $skipUsedInternallyForSecondType)
    {
        $skipUsedInternallyForSecondType->run(new stdClass());
    }
}
