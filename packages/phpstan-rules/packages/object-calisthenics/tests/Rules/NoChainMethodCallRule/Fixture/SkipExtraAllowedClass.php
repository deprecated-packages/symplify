<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\Fixture;

use Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\Source\SomeAllowedFluent;

final class SkipExtraAllowedClass
{
    public function run(SomeAllowedFluent $someAllowedFluent)
    {
        $someAllowedFluent->yes()->please()->yes();
    }
}
