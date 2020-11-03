<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule\Fixture;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules;
use PHPStan\Rules\Rule;

/**
 * @deprecated
 */
final class SkipDeprecatedRuleWithoutSee implements Rule
{
    public function getNodeType(): string
    {
    }

    public function processNode(Node $node, Scope $scope): array
    {
    }
}
