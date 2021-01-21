<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule\Fixture;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class TypicalMap
{
    public function run($phpDocTagValueNode)
    {
        if ($phpDocTagValueNode instanceof ReturnTagValueNode) {
            return '@return';
        }

        if ($phpDocTagValueNode instanceof ParamTagValueNode) {
            return '@param';
        }

        if ($phpDocTagValueNode instanceof VarTagValueNode) {
            return '@var';
        }

        throw new ShouldNotHappenException();
    }
}
