<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\Fixture;

use PHPStan\Analyser\Scope;

final class SkipNullsafeCalls
{
    public function run(Scope $scope)
    {
        return $scope->getClassReflection()?->getName();
    }
}
