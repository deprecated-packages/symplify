<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule\Fixture;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

final class RuleWithoutSee implements Rule
{
    public function getNodeType(): string
    {
    }

    public function processNode(\PhpParser\Node $node, Scope $scope): array
    {
    }
}
