<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule\Fixture;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see website
 */
final class RuleWithSeeRandom implements Rule
{
    public function getNodeType(): string
    {
    }

    public function processNode(\PhpParser\Node $node, Scope $scope): array
    {
    }
}
