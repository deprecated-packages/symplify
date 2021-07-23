<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Source\DifferentParent;

final class SkipWithCompatibleParent extends DifferentParent
{
    public function run($original)
    {
    }
}
