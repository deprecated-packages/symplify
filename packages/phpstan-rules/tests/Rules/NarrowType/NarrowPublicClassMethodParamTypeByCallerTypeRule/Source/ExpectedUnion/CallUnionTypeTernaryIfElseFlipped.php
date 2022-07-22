<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedNodeApi;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipEqualUnionType;

final class CallUnionTypeTernaryIfElseFlipped
{
    /**
     * @param MethodCall[]|StaticCall[] $data
     */
    public function run(SkipEqualUnionType $skipEqualUnionType, array $data): void
    {
        foreach ($data as $value) {
            $skipEqualUnionType->runTernaryFlipped($value);
        }
    }
}
