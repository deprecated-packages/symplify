<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\MixedAndString;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipMixedAndString;

final class FirstCaller
{
    public function run(SkipMixedAndString $skipMixedAndString)
    {
        $skipMixedAndString->resolve('string');
    }
}
