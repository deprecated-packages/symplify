<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Source\DifferentParent;

final class ClassWithDifferentParent extends DifferentParent
{
    public function run($copy)
    {
    }
}
