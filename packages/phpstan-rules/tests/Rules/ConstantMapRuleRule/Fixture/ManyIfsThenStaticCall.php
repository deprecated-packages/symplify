<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule\Fixture;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class ManyIfsThenStaticCall
{
    public function run($phpDocTagValueNode)
    {
        if ($phpDocTagValueNode instanceof ReturnTagValueNode) {
            return 1;
        }

        if ($phpDocTagValueNode instanceof ParamTagValueNode) {
            return 100;
        }

        if ($phpDocTagValueNode instanceof VarTagValueNode) {
            return 1000;
        }

        if ($phpDocTagValueNode instanceof ThrowsTagValueNode) {
            return $this->process(10000);
        }

        throw new ShouldNotHappenException();
    }

    private function process($values)
    {
        return $values;
    }
}
