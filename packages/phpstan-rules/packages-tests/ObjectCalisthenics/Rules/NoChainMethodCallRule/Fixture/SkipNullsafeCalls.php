<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\Fixture;

use PHPStan\Analyser\Scope;

final class SkipNullsafeCalls
{
    public function run(Scope $scope)
    {
        return $scope->getClassReflection()?->getName();
    }
}
