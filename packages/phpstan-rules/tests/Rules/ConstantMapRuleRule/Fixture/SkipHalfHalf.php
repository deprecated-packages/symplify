<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule\Fixture;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;

final class SkipHalfHalf
{
    public function run($phpDocTagValueNode)
    {
        if ($phpDocTagValueNode instanceof ReturnTagValueNode) {
            return '@return';
        }

        if ($this->process($phpDocTagValueNode)) {
            return false;
        }

        if ($phpDocTagValueNode instanceof ParamTagValueNode) {
            return '@param';
        }

        if ($this->process($phpDocTagValueNode)) {
            return true;
        }

        if ($phpDocTagValueNode instanceof VarTagValueNode) {
            return '@var';
        }

        if ($this->process($phpDocTagValueNode)) {
            return $phpDocTagValueNode === 0;
        }
    }

    private function process($phpDocTagValueNode): bool
    {
    }
}
