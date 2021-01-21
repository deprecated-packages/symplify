<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule\Fixture;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class SkipEmptyArray
{
    public function run($phpDocTagValueNode)
    {
        if ($phpDocTagValueNode instanceof ReturnTagValueNode) {
            return [];
        }

        if ($phpDocTagValueNode instanceof ParamTagValueNode) {
            return [];
        }

        if ($phpDocTagValueNode instanceof VarTagValueNode) {
            return [];
        }

        if ($phpDocTagValueNode instanceof ThrowsTagValueNode) {
            return [];
        }

        throw new ShouldNotHappenException();
    }
}
