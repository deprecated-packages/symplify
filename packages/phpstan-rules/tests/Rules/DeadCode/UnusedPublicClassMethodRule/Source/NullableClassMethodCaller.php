<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Source;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture\SkipNullableUsedPublicMethod;

final class NullableClassMethodCaller
{
    private function go(?SkipNullableUsedPublicMethod $skipNullableUsedPublicMethod)
    {
        $skipNullableUsedPublicMethod->useMeMaybe();
    }
}
