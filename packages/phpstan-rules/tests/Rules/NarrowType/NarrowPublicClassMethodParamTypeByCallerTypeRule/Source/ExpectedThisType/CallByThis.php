<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedThisType;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipThisPassedExactType;

final class CallByThis
{
    public function run(SkipThisPassedExactType $skipThisPassedExactType): void
    {
        $skipThisPassedExactType->run($this);
    }
}
