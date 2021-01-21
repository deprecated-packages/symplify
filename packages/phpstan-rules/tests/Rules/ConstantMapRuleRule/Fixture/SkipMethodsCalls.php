<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule\Fixture;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class SkipMethodsCalls
{
    public function run($phpDocTagValueNode)
    {
        if ($phpDocTagValueNode instanceof ReturnTagValueNode) {
            return $this->process(100);
        }

        if ($phpDocTagValueNode instanceof ParamTagValueNode) {
            return $this->process(1000);
        }

        if ($phpDocTagValueNode instanceof VarTagValueNode) {
            return $this->process(10000);
        }

        throw new ShouldNotHappenException();
    }

    private function process($values)
    {
        return $values;
    }
}
