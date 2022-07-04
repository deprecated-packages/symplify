<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Source\ParentClassWithPublicMethod;

final class SkipParentMethodOverride extends ParentClassWithPublicMethod
{
    public function parentMethod()
    {
    }
}
