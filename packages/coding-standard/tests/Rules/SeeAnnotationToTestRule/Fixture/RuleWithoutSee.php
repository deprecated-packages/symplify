<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule\Fixture;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules;
use PHPStan\Rules\Rule;

final class RuleWithoutSee implements Rule
{
    public function getNodeType(): string
    {
    }

    public function processNode(Node $node, Scope $scope): array
    {
    }
}
