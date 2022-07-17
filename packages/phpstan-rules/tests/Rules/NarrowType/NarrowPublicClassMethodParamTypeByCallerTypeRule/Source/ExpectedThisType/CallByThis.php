<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedThisType;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipThisPassedByInterface;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\SomeInterface;

final class CallByThis implements SomeInterface
{
    public function run(SkipThisPassedByInterface $skipThisPassedByInterface): void
    {
        $skipThisPassedByInterface->run($this);
    }
}
