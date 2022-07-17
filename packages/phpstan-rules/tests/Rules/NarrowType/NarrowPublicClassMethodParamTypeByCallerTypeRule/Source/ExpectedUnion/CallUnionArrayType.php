<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedNodeApi;

use PhpParser\Node;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipEqualUnionType;

final class CallUnionTypeArrayType
{
    public function run(SkipEqualUnionType $skipEqualUnionType, Node $node): void
    {
        /** @var Node[]|Node $node */
        $node = rand(0, 1)
            ? $node
            : [$node];

        $skipEqualUnionType->runArrayTyped($node);
    }

    public function run2(SkipEqualUnionType $skipEqualUnionType, Node $node): void
    {
        /** @var Node[]|Node $node */
        $node = rand(0, 1)
            ? $node
            : [$node];

        $skipEqualUnionType->runArrayTyped2($node);
    }
}
