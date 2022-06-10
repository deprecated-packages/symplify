<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\Fixture;

use Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\Source\SomeAllowedFluent;

final class SkipExtraAllowedClass
{
    public function run(SomeAllowedFluent $someAllowedFluent)
    {
        $someAllowedFluent->yes()->please()->yes();
    }
}
